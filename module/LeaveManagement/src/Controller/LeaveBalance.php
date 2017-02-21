<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 3:26 PM
 */

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use PHPExcel;
use PHPExcel_IOFactory;
use SelfService\Repository\LeaveRequestRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveBalance extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $leaveRequestRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new LeaveBalanceRepository($adapter);
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
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

        $leaveList = $this->repository->getAllLeave();

        $leaves = [];
        foreach ($leaveList as $leaveRow) {
            array_push($leaves, ['LEAVE_ENAME' => $leaveRow['LEAVE_ENAME']]);
        }

        $num = count($leaveList);

        return Helper::addFlashMessagesToArray($this, [
                    'leaveList' => $leaveList,
                    'leavesArrray' => $leaves,
                    'num' => $num,
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement,
                    'employees' => $employeeNameFormElement
        ]);
    }

    public function applyAction() {
        $request = $this->getRequest();
        $leaveId = (int) $this->params()->fromRoute('id');
        $employeeId = (int) $this->params()->fromRoute('eid');

        if ($leaveId === 0 && $employeeId === 0) {
            $postData = $request->getPost();
            $employeeId = $postData['employeeId'];
            $leaveId = $postData['leaveId'];
        }
        $this->initializeForm();

        $leaveBalanceDtl = $this->repository->getByEmpIdLeaveId($employeeId, $leaveId);
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $employeeRepository = new EmployeeRepository($this->adapter);
        $employeeDtl = $employeeRepository->fetchById($employeeId);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID) + 1;
                $leaveRequest->employeeId = $employeeId;
                $leaveRequest->leaveId = $leaveId;
                $leaveRequest->startDate = Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate = Helper::getExpressionDate($leaveRequest->endDate);
                $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "RQ";
                $this->leaveRequestRepository->add($leaveRequest);
                HeadNotification::pushNotification(NotificationEvents::LEAVE_APPLIED, $leaveRequest, $this->adapter, $this->plugin('url'));
                $this->flashmessenger()->addMessage("Leave Request Successfully added!!!");
                return $this->redirect()->toRoute("leavestatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $employeeId,
                    'leaveId' => $leaveId,
                    'employeeName' => $employeeDtl['FIRST_NAME'] . " " . $employeeDtl['MIDDLE_NAME'] . " " . $employeeDtl['LAST_NAME'],
                    'leaveName' => $leaveDtl['LEAVE_ENAME'],
                    'balance' => $leaveBalanceDtl['BALANCE'],
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function recommendApproveList($employeeId) {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach ($designations as $key => $designationList) {
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch, $withinDepartment, $designationId, $branchId, $departmentId);

            if ($key == 1) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            } else if ($key == 2) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver" => $approver
        ];
        return $responseData;
    }

    public function exportAction() {
        $leaveBalanceList = $this->repository->getOnlyCarryForwardedRecord();
        $num = count($leaveBalanceList);
        if ($num == 0) {
            $this->flashmessenger()->addMessage("There is no record found to export!!!");
            return $this->redirect()->toRoute("leavebalance");
        } else {

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            $rowCount = 1;
            $column = 'A';
            foreach ($leaveBalanceList[0] as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $key);
                $column++;
            }

            $rowCount = 2;
            foreach ($leaveBalanceList as $leaveBalanceRow) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $leaveBalanceRow['EMPLOYEE_ID']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $leaveBalanceRow['LEAVE_ID']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $leaveBalanceRow['PREVIOUS_YEAR_BAL']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $leaveBalanceRow['TOTAL_DAYS']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $leaveBalanceRow['BALANCE']);
                $rowCount++;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="LeaveBalance.xlsx"');
            header('Cache-Control: max-age=0');

            ob_end_clean();
            $objWriter->save('php://output');
            exit;
        }
    }

}
