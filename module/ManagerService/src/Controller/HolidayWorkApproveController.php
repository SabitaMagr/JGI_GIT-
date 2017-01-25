<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use ManagerService\Repository\HolidayWorkApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\WorkOnHolidayRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use HolidayManagement\Model\Holiday;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Repository\HolidayRepository;

class HolidayWorkApproveController extends AbstractActionController {

    private $holidayWorkApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->holidayWorkApproveRepository = new HolidayWorkApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnHolidayForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        //print_r($this->employeeId); die();
        $list = $this->holidayWorkApproveRepository->getAllRequest($this->employeeId);

        $holidayWorkApprove = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getStatusValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
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
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            $dataArray = [
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'FROM_DATE' => $row['FROM_DATE'],
                'TO_DATE' => $row['TO_DATE'],
                'DURATION' => $row['DURATION'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REMARKS' => $row['REMARKS'],
                'HOLIDAY_ENAME' => $row['HOLIDAY_ENAME'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'ID' => $row['ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($holidayWorkApprove, $dataArray);
        }
        return Helper::addFlashMessagesToArray($this, ['holidayWorkApprove' => $holidayWorkApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        $workOnHolidayModel = new WorkOnHoliday();
        $request = $this->getRequest();

        $detail = $this->holidayWorkApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $RECM_MN = ($detail['RECM_MN'] != null) ? " " . $detail['RECM_MN'] . " " : " ";
        $recommender = $detail['RECM_FN'] . $RECM_MN . $detail['RECM_LN'];
        $APRV_MN = ($detail['APRV_MN'] != null) ? " " . $detail['APRV_MN'] . " " : " ";
        $approver = $detail['APRV_FN'] . $APRV_MN . $detail['APRV_LN'];
        $MN1 = ($detail['MN1'] != null) ? " " . $detail['MN1'] . " " : " ";
        $recommended_by = $detail['FN1'] . $MN1 . $detail['LN1'];
        $MN2 = ($detail['MN2'] != null) ? " " . $detail['MN2'] . " " : " ";
        $approved_by = $detail['FN2'] . $MN2 . $detail['LN2'];
        $authRecommender = ($status == 'RQ') ? $recommender : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || ($status == 'R' && $approvedDT == null)) ? $approver : $approved_by;
        $recommenderId = ($status == 'RQ') ? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        if (!$request->isPost()) {
            $workOnHolidayModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnHolidayModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
                } else if ($action == "Approve") {
                    $workOnHolidayModel->status = "RC";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved!!!");
                }
                $workOnHolidayModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                $workOnHolidayModel->id = $id;
                //HeadNotification::pushNotification(($loanRequestModel->status == 'RC') ? NotificationEvents::LOAN_RECOMMEND_ACCEPTED : NotificationEvents::LOAN_RECOMMEND_REJECTED, $loanRequestModel, $this->adapter, $this->plugin('url'));
            } else if ($role == 3 || $role == 4) {
                $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
                } else if ($action == "Approve") {
                    $workOnHolidayModel->status = "AP";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved");
                }
                if ($role == 4) {
                    $workOnHolidayModel->recommendedBy = $this->employeeId;
                    $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $workOnHolidayModel->approvedRemarks = $getData->approvedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                $workOnHolidayModel->id = $id;
                //HeadNotification::pushNotification(($loanRequestModel->status == 'AP') ? NotificationEvents::LOAN_APPROVE_ACCEPTED : NotificationEvents::LOAN_APPROVE_REJECTED, $loanRequestModel, $this->adapter, $this->plugin('url'));
            }
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'status' => $status,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'employeeId' => $this->employeeId,
                    'requestedEmployeeId' => $requestedEmployeeID,
                    'holidays' => $this->getHolidayList($requestedEmployeeID)
        ]);
    }

    public function statusAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC");
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC");
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = EntityHelper::getTableKVList($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => 'E']);
        $holidays1 = [-1 => "All"] + $holidays;
        $holidayFormElement->setValueOptions($holidays1);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday Type");

        $holidayStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'holidays' => $holidayFormElement,
                    'employees' => $employeeNameFormElement,
                    'status' => $statusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }
    public function getHolidayList($employeeId){
       $holidayRepo = new HolidayRepository($this->adapter);
       $holidayResult = $holidayRepo->selectAll($employeeId);
       $holidayList = [];
       foreach($holidayResult as $holidayRow){
           //$todayDate = new \DateTime();
           $holidayList[$holidayRow['HOLIDAY_ID']]=$holidayRow['HOLIDAY_ENAME']." (".$holidayRow['START_DATE']." to ".$holidayRow['END_DATE'].")";
       }
       return $holidayList;
    }
}
