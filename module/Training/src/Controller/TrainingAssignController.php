<?php

namespace Training\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Model\Training;
use Training\Form\TrainingAssignForm;
use Training\Repository\TrainingAssignRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingAssignController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TrainingAssignRepository($this->adapter);
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
        $serviceEventTypes1 = [-1 => "All"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $trainingFormElement = new Select();
        $trainingFormElement->setName("training");
        $trainings = EntityHelper::getTableKVListWithSortOption($this->adapter, Training::TABLE_NAME, Training::TRAINING_ID, [Training::TRAINING_NAME], [Training::STATUS => 'E'], "TRAINING_NAME", "ASC");
        $trainings1 = [-1 => "All"] + $trainings;
        $trainingFormElement->setValueOptions($trainings1);
        $trainingFormElement->setAttributes(["id" => "trainingId", "class" => "form-control"]);
        $trainingFormElement->setLabel("Training");

        return Helper::addFlashMessagesToArray($this, [
                    'list' => 'list',
                    'employees' => $employeeNameFormElement,
                    'branches' => $branchFormElement,
                    'departments' => $departmentFormElement,
                    'positions' => $positionFormElement,
                    'designations' => $designationFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'trainings' => $trainingFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingAssignForm();
        $this->form = $builder->createForm($form);
    }

    public function addAction() {
        $this->initializeForm();
        $employee = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E'], " ");
        $trainingList = array(
            '1' => 'Organizational Hard Skills',
            '2' => 'Organizational',
            '3' => 'Organizational Soft Skills'
        );
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees' => $employee,
                    'training' => $trainingList
        ]);
    }

    public function assignAction() {
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

        $trainingFormElement = new Select();
        $trainingFormElement->setName("training");
        $trainings = EntityHelper::getTableKVListWithSortOption($this->adapter, Training::TABLE_NAME, Training::TRAINING_ID, [Training::TRAINING_NAME], [Training::STATUS => 'E'], "TRAINING_NAME", "ASC");
        $trainingFormElement->setValueOptions($trainings);
        $trainingFormElement->setAttributes(["id" => "trainingId", "class" => "form-control"]);
        $trainingFormElement->setLabel("Training");

        return Helper::addFlashMessagesToArray($this, [
                    'list' => 'list',
                    'employees' => $employeeNameFormElement,
                    'branches' => $branchFormElement,
                    'departments' => $departmentFormElement,
                    'positions' => $positionFormElement,
                    'designations' => $designationFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'trainings' => $trainingFormElement
        ]);
    }

    public function deleteAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");
        if (!$trainingId && !$employeeId) {
            return $this->redirect()->toRoute('trainingAssign');
        }
        //print_r("hellow"); die();
        $this->repository->delete([$employeeId, $trainingId]);
        $model = new \Training\Model\TrainingAssign();
        $model->trainingId = $trainingId;
        $model->employeeId = $employeeId;
        HeadNotification::pushNotification(NotificationEvents::TRAINING_CANCELLED, $model, $this->adapter, $this->plugin('url'));
        $this->flashmessenger()->addMessage("Training Assign Successfully Cancelled!!!");
        return $this->redirect()->toRoute('trainingAssign');
    }

    public function viewAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");

        if (!$employeeId && !$trainingId) {
            return $this->redirect()->toRoute('trainingAssign');
        }

        $detail = $this->repository->getDetailByEmployeeID($employeeId, $trainingId);

        return Helper::addFlashMessagesToArray($this, ['detail' => $detail]);
    }

}
