<?php

namespace AttendanceManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\AttendanceDetail as AttendanceByHrModel;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Exception;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AttendanceByHr extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceDetailRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceByHr = new AttendanceByHrForm();
        $this->form = $builder->createForm($attendanceByHr);
    }

    public function indexAction() {
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

        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        $attendanceList = $this->repository->fetchAll();
        $attendanceByHr = [];
        foreach ($attendanceList as $attendanceRow) {
            array_push($attendanceByHr, $attendanceRow);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'attendanceByHr' => $attendanceByHr,
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'employees' => $employeeNameFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement,
                    'status' => $statusFormElement
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        try {


            if ($request->isPost()) {
                $this->form->setData($request->getPost());
                if ($this->form->isValid()) {
                    $attendanceByHrModel = new AttendanceByHrModel();
                    $formData = $this->form->getData();
                    $attendanceByHrModel->exchangeArrayFromForm($formData);
                    $attendanceByHrModel->attendanceDt = Helper::getExpressionDate($attendanceByHrModel->attendanceDt);
                    $attendanceByHrModel->id = ((int) Helper::getMaxId($this->adapter, AttendanceByHrModel::TABLE_NAME, "ID")) + 1;
                    $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                    $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);

                    $employeeId = $attendanceByHrModel->employeeId;
                    $attendanceDt = $formData['attendanceDt'];

                    $previousDtl = $this->repository->getDtlWidEmpIdDate($employeeId, $attendanceDt);

                    if ($previousDtl == null) {
                        throw new Exception("Attendance of employee with employeeId :$employeeId on $attendanceDt is not found.");
                    } else {
                        $this->repository->edit($attendanceByHrModel, $previousDtl['ID']);
                    }

                    $this->flashmessenger()->addMessage("Attendance Submitted Successfully!!");
                    return $this->redirect()->toRoute("attendancebyhr");
                }
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ")
                            ]
            );
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
            return $this->redirect()->toRoute("attendancebyhr");
        }
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("attendancebyhr");
        }

        $request = $this->getRequest();
        $attendanceByHrModel = new AttendanceByHrModel();
        if (!$request->isPost()) {
            $attendanceByHrModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($attendanceByHrModel);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceByHrModel->exchangeArrayFromForm($this->form->getData());
                $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);
                $this->repository->edit($attendanceByHrModel, $id);
                $this->flashmessenger()->addMessage("Attendance Updated Successfully!!");
                return $this->redirect()->toRoute("attendancebyhr");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC")
                        ]
        );
    }

    public function deleteAction() {
        
    }

}
