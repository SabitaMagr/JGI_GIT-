<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 5:05 PM
 */

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Exception;
use ManagerService\Repository\AttendanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

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
        $getStatusValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        foreach ($list as $row) {
            $new_row = array_merge($row, [
                'YOUR_ROLE' => 'APPROVER',
                'STATUS' => $getStatusValue($row['STATUS'])
            ]);
            array_push($attendanceApprove, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['attendanceApprove' => $attendanceApprove]);
    }

    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("attedanceapprove");
        }
        $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $attendanceRequestRepository->fetchById($id);
        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $fullName($employeeId);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];
        $approved_by = $fullName($detail['APPROVED_BY']);
        $approverName = $fullName($detail['APPROVER']);
        $authApprover = ( $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

        $attendanceDetail = new AttendanceDetail();
        $attendanceRepository = new AttendanceDetailRepository($this->adapter);

        try {
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
//                    $attendanceRepository->add($attendanceDetail);
                        throw new Exception("Attendance of employee with employeeId :$employeeId on $attendanceDt is not found.");
                    } else {
                        $attendanceRepository->edit($attendanceDetail, $previousDtl['ID']);
                    }
                    $this->flashmessenger()->addMessage("Attendance Request Approved!!!");
                } else if ($action == "Reject") {
                    $model->status = "R";
                    $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
                }
                $model->approvedBy = $this->employeeId;
                $model->approvedRemarks = $reason;
                $this->repository->edit($model, $id);
                $model->id = $id;
                try {
                    HeadNotification::pushNotification(($model->status == 'AP') ? NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED : NotificationEvents::ATTENDANCE_APPROVE_REJECTED, $model, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
                return $this->redirect()->toRoute("attedanceapprove");
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'status' => $detail['STATUS'],
                        'employeeName' => $employeeName,
                        'employeeId' => $employeeId,
                        'approver' => $authApprover,
                        'requestedDt' => $detail['REQUESTED_DT'],
            ]);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'status' => $detail['STATUS'],
                        'employeeName' => $employeeName,
                        'employeeId' => $employeeId,
                        'approver' => $authApprover,
                        'requestedDt' => $detail['REQUESTED_DT'],
            ]);
        }
    }

    public function statusAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
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

        $attendanceStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $attendanceStatusFormElement = new Select();
        $attendanceStatusFormElement->setName("attendanceStatus");
        $attendanceStatusFormElement->setValueOptions($attendanceStatus);
        $attendanceStatusFormElement->setAttributes(["id" => "attendanceRequestStatusId", "class" => "form-control"]);
        $attendanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'employees' => $employeeNameFormElement,
                    'attendanceStatus' => $attendanceStatusFormElement,
                    'approverId' => $this->employeeId,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }

}
