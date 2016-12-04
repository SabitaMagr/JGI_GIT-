<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 5:05 PM
 */

namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use ManagerService\Repository\AttendanceApproveRepository;
use Application\Helper\Helper;
use SelfService\Form\AttendanceRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\AttendanceRequestModel;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;

class AttendanceApproveController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceApproveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm() {
        $attendanceRequestForm = new AttendanceRequestForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceRequestForm);
    }

    public function indexAction() {
        $list = $this->repository->getAllRequest($this->employeeId, 'RQ');
        $attendanceApprove = [];
        foreach($list as $row){
            $new_row = array_merge($row,['YOUR_ROLE'=>'APPROVER']);
            array_push($attendanceApprove,$new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['attendanceApprove' => $attendanceApprove]);
    }

    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("attedanceapprove");
        }

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $this->repository->fetchById($id);
        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];

        $attendanceDetail = new AttendanceDetail();
        $attendanceRepository = new AttendanceDetailRepository($this->adapter);

        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($detail);
            $this->form->bind($model);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $model->approvedDt = Helper::getcurrentExpressionDate();

            if ($action == "Approve") {
                $model->status = "AP";
                $attendanceDetail->attendanceDt = Helper::getcurrentExpressionDate($detail['ATTENDANCE_DT']);
                $attendanceDetail->inTime = Helper::getExpressionTime($detail['IN_TIME']);
                $attendanceDetail->inRemarks = $detail['IN_REMARKS'];
                $attendanceDetail->outTime = Helper::getExpressionTime($detail['OUT_TIME']);
                $attendanceDetail->outRemarks = $detail['OUT_REMARKS'];
                $attendanceDetail->totalHour = $detail['TOTAL_HOUR'];
                $attendanceDetail->employeeId = $detail['EMPLOYEE_ID'];
                $attendanceDetail->id = (int) Helper::getMaxId($this->adapter, AttendanceDetail::TABLE_NAME, AttendanceDetail::ID) + 1;

                $employeeId = $detail['EMPLOYEE_ID'];
                $attendanceDt = $detail['ATTENDANCE_DT'];

                $previousDtl = $attendanceRepository->getDtlWidEmpIdDate($employeeId, $attendanceDt);

                if ($previousDtl == null) {
                    $attendanceRepository->add($attendanceDetail);
                } else {
                    $attendanceRepository->edit($attendanceDetail, $previousDtl['ID']);
                }

                $this->flashmessenger()->addMessage("Attendance Request Approved!!!");
            } else if ($action == "Reject") {
                $model->status = "R";
                $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
            }
            $model->approvedRemarks = $reason;
            $this->repository->edit($model, $id);
            return $this->redirect()->toRoute("attedanceapprove");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'status' => $detail['STATUS'],
                    'employeeName' => $employeeName,
                    'employeeId' => $employeeId,
                    'requestedDt' => $detail['REQUESTED_DT'],
        ]);
    }

    public function statusAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = \Application\Helper\EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"]);
        $employeeName[-1] = "All";
        ksort($employeeName);
        $employeeNameFormElement->setValueOptions($employeeName);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = \Application\Helper\EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E']);
        $branches[-1] = "All";
        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");


        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = \Application\Helper\EntityHelper::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E']);
        $departments[-1] = "All";
        ksort($departments);
        $departmentFormElement->setValueOptions($departments);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = \Application\Helper\EntityHelper::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E']);
        $designations[-1] = "All";
        ksort($designations);
        $designationFormElement->setValueOptions($designations);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = \Application\Helper\EntityHelper::getTableKVList($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E']);
        $positions[-1] = "All";
        ksort($positions);
        $positionFormElement->setValueOptions($positions);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = \Application\Helper\EntityHelper::getTableKVList($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E']);
        $serviceTypes[-1] = "All";
        ksort($serviceTypes);
        $serviceTypeFormElement->setValueOptions($serviceTypes);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");
        
        $attendanceStatus = [
            '-1'=>'All',
            'RQ'=>'Pending',
            'AP'=>'Approved',
            'R'=>'Rejected'
        ];
        $attendanceStatusFormElement = new Select();
        $attendanceStatusFormElement->setName("attendanceStatus");
        $attendanceStatusFormElement->setValueOptions($attendanceStatus);
        $attendanceStatusFormElement->setAttributes(["id" => "attendanceRequestStatusId", "class" => "form-control"]);
        $attendanceStatusFormElement->setLabel("Status");
        
        return Helper::addFlashMessagesToArray($this,[
            "branches" => $branchFormElement,
            "departments" => $departmentFormElement,
            'designations' => $designationFormElement,
            'positions' => $positionFormElement,
            'serviceTypes' => $serviceTypeFormElement,
            'employees' => $employeeNameFormElement,
            'attendanceStatus'=>$attendanceStatusFormElement,
            'approverId'=>$this->employeeId
        ]);

    }

}
