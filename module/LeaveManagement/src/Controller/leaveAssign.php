<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/8/16
 * Time: 5:17 PM
 */

namespace LeaveManagement\Controller;

use Application\Helper\Helper;
use LeaveManagement\Form\ExcelImportForm;
use LeaveManagement\Form\LeaveAssignForm;
use LeaveManagement\Helper\EntityHelper;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveAssignRepository;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use Setup\Controller\EmployeeController;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\ServiceType;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Application\Helper\EntityHelper as AppEntityHelper;

class leaveAssign extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $excelImportForm;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveAssignRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $leaveAssignForm = new LeaveAssignForm();
        $excelImportForm = new ExcelImportForm();
        $this->builder = new AnnotationBuilder();
        $this->form = $this->builder->createForm($leaveAssignForm);
        $this->excelImportForm = $this->builder->createForm($excelImportForm);
    }

    public function indexAction() {
        $excelImportForm = new ExcelImportForm();
        $form = $this->builder->createForm($excelImportForm);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeList = $employeeRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
                    'employeeList' => $employeeList
        ]);
    }

    public function assignAction() {
        $this->initializeForm();

//        $empFormElement = new Select();
//        $empFormElement->setName("employee");
//        $empFormElement->setValueOptions(\Application\Helper\EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"]));
//        $empFormElement->setAttributes(["id" => "employeeId", "class" => "form-control", "data-init-plugin" => "select2"]);

        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = AppEntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E","RETIRED_FLAG"=>"N"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");
        
        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaveFormElement->setLabel("Leave Type");
        $leaveFormElement->setValueOptions(AppEntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS . " ='E'"], LeaveMaster::LEAVE_ENAME,"ASC"));
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control", "data-init-plugin" => "select2"]);

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = AppEntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = AppEntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");
        
        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = AppEntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], ServiceType::SERVICE_TYPE_NAME, "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genders = AppEntityHelper::getTableKVList($this->adapter, "HRIS_GENDERS", "GENDER_ID", ["GENDER_NAME"]);
        $genders[-1] = "All";
        ksort($genders);
        $genderFormElement->setValueOptions($genders);
        $genderFormElement->setAttributes(["id" => "genderId", "class" => "form-control", "data-init-plugin" => "select2"]);
        $genderFormElement->setLabel("Gender");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = AppEntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        return Helper::addFlashMessagesToArray($this, [
                    'leaveFormElement' => $leaveFormElement,
                    'branchFormElement' => $branchFormElement,
                    'departmentFormElement' => $departmentFormElement,
                    'genderFormElement' => $genderFormElement,
                    'designationFormElement' => $designationFormElement,
                    'serviceTypeFormElement'=>$serviceTypeFormElement,
                    'employeeFormElement'=>$employeeNameFormElement,
                    'form' => $this->excelImportForm
        ]);
    }

//    public function assignAction()
//    {
//        $this->initializeForm();
//        $id = (int)$this->params()->fromRoute("eid");
//
//        if ($id === 0) {
//            return $this->redirect()->toRoute("leaveassign");
//        }
//        $employeeRepo = new EmployeeRepository($this->adapter);
//        $employee = $employeeRepo->fetchById($id);
//
//        $assignList = $this->repository->fetchByEmployeeId($id);
//        return Helper::addFlashMessagesToArray($this, [
//            'assignList' => $assignList,
//            'id' => $id,
//            'employee' => $employee
//        ]);
//    }

    public function addAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("eid");

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchById($id);

        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
                $leaveAssign->exchangeArrayFromForm($this->form->getData());
                // $leaveAssign->employeeLeaveAssignId = ((int) Helper::getMaxId($this->adapter, LeaveAssignController::TABLE_NAME, LeaveAssignController::EMPLOYEE_LEAVE_ASSIGN_ID)) + 1;
                $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                $leaveAssign->createdBy = $this->employeeId;
                $leaveAssign->employeeId = $id;
                $this->repository->add($leaveAssign);
                $this->flashmessenger()->addMessage("Leave assigned Successfully!!!");
                return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $id]);
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'eid' => $id,
                    'employee' => $employee,
                    'leavelist' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_LEAVE_MASTER_SETUP)
                        ]
        );
    }

    public function editAction() {
        $this->initializeForm();
        $eid = (int) $this->params()->fromRoute("eid");
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0 || $eid === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }

        $request = $this->getRequest();
        $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
        $employee = new HrEmployees();
        if (!$request->isPost()) {
            $leaveAssign->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($leaveAssign);
            $employee->employeeId = $leaveAssign->employeeId;
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveAssign->exchangeArrayFromForm($this->form->getData());
                $leaveAssign->modifiedDt = Helper::getcurrentExpressionDate();
                $leaveAssign->modifiedBy = $this->employeeId;
                
                unset($leaveAssign->employeeLeaveAssignId);
                unset($leaveAssign->createdDt);
                $this->repository->edit($leaveAssign, $id);
                $this->flashmessenger()->addMessage("Assigned leave Successfuly Updated!!!");
                return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $eid]);
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'eid' => $eid,
                    'employee' => $employee,
                    'leavelist' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_LEAVE_MASTER_SETUP)
                        ]
        );
    }

    public function deleteAction() {
        $eid = (int) $this->params()->fromRoute("eid");
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0 || $eid === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Assigned Leave Successfully Deleted!!!");
        return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $eid]);
    }

    public function importAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveBalanceRepository = new LeaveBalanceRepository($this->adapter);

        if ($request->isPost()) {
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $this->excelImportForm->setData($post);

            if ($this->excelImportForm->isValid()) {
                $data = $this->excelImportForm->getData();
                $files = $request->getFiles()->toArray();

                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);

                $file = Helper::UPLOAD_DIR . "/" . $newFileName;

                $objPHPExcel = \PHPExcel_IOFactory::load($file);
                $dataArr = array();

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

                    for ($row = 2; $row <= $highestRow; ++$row) {
                        for ($col = 0; $col < $highestColumnIndex; ++$col) {
                            $cell = $worksheet->getCellByColumnAndRow($col, $row);
                            $val = $cell->getValue();
                            $dataArr[$row][$col] = $val;
                        }
                    }
                }

                foreach ($dataArr as $row) {
                    $employeeId = $row[0];
                    $leaveId = $row[1];
                    $preYrBalance = $row[2];
                    $totalDays = $row[3];
                    $balance = $preYrBalance + $totalDays;

                    $leaveDetail = $leaveRepository->fetchById($leaveId);
                    if ($leaveDetail['STATUS'] == 'E') {
                        $empLeaveBalanceDetail = $leaveBalanceRepository->getByEmpIdLeaveId($employeeId, $leaveId);
                        if ($empLeaveBalanceDetail == NULL) {
                            $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
                            //$leaveAssign->employeeLeaveAssignId = ((int) Helper::getMaxId($this->adapter, LeaveAssignController::TABLE_NAME, LeaveAssignController::EMPLOYEE_LEAVE_ASSIGN_ID)) + 1;
                            $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                            $leaveAssign->createdBy = $this->employeeId;
                            $leaveAssign->employeeId = $employeeId;
                            $leaveAssign->leaveId = $leaveId;
                            $leaveAssign->totalDays = $totalDays;
                            $leaveAssign->previousYearBalance = $preYrBalance;
                            $leaveAssign->balance = $balance;
                            $this->repository->add($leaveAssign);
                        } else {
                            if ($leaveDetail['CARRY_FORWARD'] == 'Y') {
                                $updatePreYrBalance = $this->repository->updatePreYrBalance($employeeId, $leaveId, $preYrBalance, $totalDays, $balance);
                            }
                        }
                    }
                }
                unlink(Helper::UPLOAD_DIR . "/" . $newFileName);
            }

            $viewModel = new ViewModel(['data' => ['success' => true]]);
            $viewModel->setTerminal(true);
            $viewModel->setTemplate('layout/json');
            return $viewModel;
        }
    }

}
