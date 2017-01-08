<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:04 PM
 */

namespace LeaveManagement\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use LeaveManagement\Repository\LeaveStatusRepository;
use ManagerService\Repository\LeaveApproveRepository;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use LeaveManagement\Form\LeaveApplyForm;
use Zend\Form\Annotation\AnnotationBuilder;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveMasterRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use LeaveManagement\Model\LeaveMaster;

class LeaveStatus extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveStatusRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
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

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaves = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E'], LeaveMaster::LEAVE_ENAME, "ASC");
        $leaves1 = [-1 => "All"] + $leaves;
        $leaveFormElement->setValueOptions($leaves1);
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control"]);
        $leaveFormElement->setLabel("Type");

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC");
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $leaveStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $leaveStatusFormElement = new Select();
        $leaveStatusFormElement->setName("leaveStatus");
        $leaveStatusFormElement->setValueOptions($leaveStatus);
        $leaveStatusFormElement->setAttributes(["id" => "leaveRequestStatusId", "class" => "form-control"]);
        $leaveStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'leaves' => $leaveFormElement,
                    'employees' => $employeeNameFormElement,
                    'leaveStatus' => $leaveStatusFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }

    public function viewAction() {
        $this->initializeForm();
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("leavestatus");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        //print_r($detail); die();

        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $recommender = $detail['FN1'] . " " . $detail['MN1'] . " " . $detail['LN1'];
        $approver = $detail['FN2'] . " " . $detail['MN2'] . " " . $detail['LN2'];
        $status = $detail['STATUS'];

        //to get the previous balance of selected leave from assigned leave detail
        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if (!$request->isPost()) {
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $leaveApply->status = "R";
                $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
            } else if ($action == "Approve") {
                $leaveApply->status = "AP";

                if ($detail['HALF_DAY'] != 'N') {
                    $leaveTaken = 0.5;
                } else {
                    $leaveTaken = $detail['NO_OF_DAYS'];
                }
                $newBalance = $preBalance - $leaveTaken;
                //to update the previous balance
                $leaveApproveRepository->updateLeaveBalance($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'], $newBalance);

                $this->flashmessenger()->addMessage("Leave Request Approved");
            }
            unset($leaveApply->halfDay);
            $leaveApply->approvedRemarks = $reason;
            $leaveApproveRepository->edit($leaveApply, $id);

            return $this->redirect()->toRoute("leavestatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DT'],
                    'availableDays' => $preBalance,
                    'totalDays' => $result['TOTAL_DAYS'],
                    'recommender' => $recommender,
                    'approver' => $approver,
                    'approvedDT'=>$detail['APPROVED_DT'],
                    'remarkDtl' => $detail['REMARKS'],
                    'status' => $status,
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

}
