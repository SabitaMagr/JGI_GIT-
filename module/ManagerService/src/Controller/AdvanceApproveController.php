<?php

namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use ManagerService\Repository\AdvanceApproveRepository;
use Zend\Authentication\AuthenticationService;
use SelfService\Repository\AdvanceRequestRepository;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest;
use Zend\Form\Annotation\AnnotationBuilder;
use Setup\Model\Advance;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Setup\Model\ServiceEventType;
use Zend\Form\Element\Select;

class AdvanceApproveController extends AbstractActionController {
    private $advanceApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;
        
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->advanceApproveRepository = new AdvanceApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new AdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        //print_r($this->employeeId); die();
        $list = $this->advanceApproveRepository->getAllRequest($this->employeeId);

        $advanceApprove = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getRole = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 2;
            } else if ($this->employeeId == $approver) {
                return 3;
            }
        };
        foreach ($list as $row) {
            array_push($advanceApprove, [
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'ADVANCE_DATE' => $row['ADVANCE_DATE'],
                'REQUESTED_AMOUNT' => $row['REQUESTED_AMOUNT'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REASON' => $row['REASON'],
                'TERMS' => $row['TERMS'],
                'ADVANCE_NAME' => $row['ADVANCE_NAME'],
                'ADVANCE_REQUEST_ID' => $row['ADVANCE_REQUEST_ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ]);
        }
        //print_r($advanceApprove); die();
        return Helper::addFlashMessagesToArray($this, ['advanceApprove' => $advanceApprove, 'id' => $this->employeeId]);
    }
    
    public function viewAction() {
        $this->initializeForm();
        $advanceRequestRepository = new AdvanceRequestRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("advanceApprove");
        }
        $advanceRequestModel = new AdvanceRequest();
        $request = $this->getRequest();

        $detail = $this->advanceApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];        
        $RECM_MN = ($detail['RECM_MN']!=null)? " ".$detail['RECM_MN']." ":" ";
        $recommender = $detail['RECM_FN'].$RECM_MN.$detail['RECM_LN'];        
        $APRV_MN = ($detail['APRV_MN']!=null)? " ".$detail['APRV_MN']." ":" ";
        $approver = $detail['APRV_FN'].$APRV_MN.$detail['APRV_LN'];
        $MN1 = ($detail['MN1']!=null)? " ".$detail['MN1']." ":" ";
        $recommended_by = $detail['FN1'].$MN1.$detail['LN1'];        
        $MN2 = ($detail['MN2']!=null)? " ".$detail['MN2']." ":" ";
        $approved_by = $detail['FN2'].$MN2.$detail['LN2'];
        $authRecommender = ($status=='RQ')?$recommender:$recommended_by;
        $authApprover = ($status=='RC' || $status=='RQ' || ($status=='R' && $approvedDT==null))?$approver:$approved_by;

        if (!$request->isPost()) {
            $advanceRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($advanceRequestModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $advanceRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $advanceRequestModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $advanceRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $advanceRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Advance Request Approved!!!");
                }
                $advanceRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->advanceApproveRepository->edit($advanceRequestModel, $id);
            } else if ($role == 3) {
                $advanceRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $advanceRequestModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $advanceRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $advanceRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Advance Request Approved");
                }
                $advanceRequestModel->approvedRemarks = $getData->approvedRemarks;
                $this->advanceApproveRepository->edit($advanceRequestModel, $id);
            }
            return $this->redirect()->toRoute("advanceApprove");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'recommender'=>$authRecommender,
                    'approver'=>$authApprover,
                    'status' => $status,
                    'recommendedBy' => $detail['RECOMMENDER'],
                    'approvedDT'=>$approvedDT,
                    'employeeId' => $this->employeeId,
                    'requestedEmployeeId' => $requestedEmployeeID,
                    'advances' => EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => "E"], Advance::ADVANCE_ID, "ASC")
        ]);
    }
    
    public function statusAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC");
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC");
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $advanceFormElement = new Select();
        $advanceFormElement->setName("advance");
        $advances = \Application\Helper\EntityHelper::getTableKVList($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => 'E']);
        $advances1 = [-1 => "All"] + $advances;
        $advanceFormElement->setValueOptions($advances1);
        $advanceFormElement->setAttributes(["id" => "advanceId", "class" => "form-control"]);
        $advanceFormElement->setLabel("Advance Type");

        $advanceStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $advanceStatusFormElement = new Select();
        $advanceStatusFormElement->setName("advanceStatus");
        $advanceStatusFormElement->setValueOptions($advanceStatus);
        $advanceStatusFormElement->setAttributes(["id" => "advanceRequestStatusId", "class" => "form-control"]);
        $advanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'advances' => $advanceFormElement,
                    'employees' => $employeeNameFormElement,
                    'advanceStatus' => $advanceStatusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }

}
