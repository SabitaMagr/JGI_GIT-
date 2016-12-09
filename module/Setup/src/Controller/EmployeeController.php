<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 11:02 AM
 */

namespace Setup\Controller;

use Application\Helper\EntityHelper as ApplicationHelper;
use Application\Helper\Helper;
use Setup\Form\HrEmployeesFormTabSix;
use Setup\Helper\EntityHelper;
use Setup\Form\HrEmployeesFormTabFive;
use Setup\Form\HrEmployeesFormTabFour;
use Setup\Form\HrEmployeesFormTabOne;
use Setup\Form\HrEmployeesFormTabThree;
use Setup\Form\HrEmployeesFormTabTwo;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Setup\Model\ServiceEventType;
use Zend\Form\Element\Select;

class EmployeeController extends AbstractActionController {

    private $adapter;
    private $form;
    private $repository;
    private $employeeFileRepo;

    const UPLOAD_DIR = "/var/www/html/neo-hris/public/uploads";

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new EmployeeRepository($adapter);
        $this->employeeFileRepo = new EmployeeFile($this->adapter);
    }

    public function indexAction() {
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

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVList($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E']);
        $serviceEventTypes[-1] = "All";
        ksort($serviceEventTypes);
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $employees = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
                    'list' => $employees,
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement,
                    'employees' => $employeeNameFormElement
        ]);
    }

    private $formOne;
    private $formTwo;
    private $formThree;
    private $formFour;
    private $formSix;

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $formTabOne = new HrEmployeesFormTabOne();
        $formTabTwo = new HrEmployeesFormTabTwo();
        $formTabThree = new HrEmployeesFormTabThree();
        $formTabFour = new HrEmployeesFormTabFour();
        $formTabFive = new HrEmployeesFormTabFive();
        $formTabSix = new HrEmployeesFormTabSix();

        if (!$this->formOne) {
            $this->formOne = $builder->createForm($formTabOne);
        }
        if (!$this->formTwo) {
            $this->formTwo = $builder->createForm($formTabTwo);
        }
        if (!$this->formThree) {
            $this->formThree = $builder->createForm($formTabThree);
        }
        if (!$this->formFour) {
            $this->formFour = $builder->createForm($formTabFour);
        }
        if (!$this->formSix) {
            $this->formSix = $builder->createForm($formTabSix);
        }
    }

    public function addAction() {
        $this->initializeForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->formOne->setData($postData);
            if ($this->formOne->isValid()) {
                $formOneModel = new HrEmployeesFormTabOne();
                $formOneModel->exchangeArrayFromForm($this->formOne->getData());
                $formOneModel->employeeId = ((int) Helper::getMaxId($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID")) + 1;
                $formOneModel->status = 'E';
                $formOneModel->createdDt = Helper::getcurrentExpressionDate();
                $formOneModel->birthDate = Helper::getExpressionDate($formOneModel->birthDate);
                $formOneModel->addrPermCountryId = 168;
                $formOneModel->addrTempCountryId = 168;
                $this->repository->add($formOneModel);
                return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $formOneModel->employeeId, 'tab' => 2]);
            }
        }
        $rankTypes = array(
            'GPA' => "GPA",
            'PER' => 'Percentage'
        );
        return new ViewModel([
            'formOne' => $this->formOne,
            'formTwo' => $this->formTwo,
            'formThree' => $this->formThree,
            'formFour' => $this->formFour,
            'formSix' => $this->formSix,
            "bloodGroups" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BLOOD_GROUPS),
            "districts" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS),
            "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
            "vdcMunicipalities" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY),
            "zones" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_ZONES),
            "religions" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_RELIGIONS),
            "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY),
            "countries" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COUNTRIES),
            'filetypes' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_FILE_TYPE),
            'serviceTypes' => ApplicationHelper::getTableKVList($this->adapter, "HR_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E']),
            'positions' => ApplicationHelper::getTableKVList($this->adapter, "HR_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E']),
            'designations' => ApplicationHelper::getTableKVList($this->adapter, "HR_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E']),
            'departments' => ApplicationHelper::getTableKVList($this->adapter, "HR_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E']),
            'branches' => ApplicationHelper::getTableKVList($this->adapter, "HR_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E']),
            'serviceEventTypes' => ApplicationHelper::getTableKVList($this->adapter, "HR_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E']),
            'academicDegree' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_DEGREES", "ACADEMIC_DEGREE_ID", ["ACADEMIC_DEGREE_NAME"], ["STATUS" => 'E']),
            'academicUniversity' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_UNIVERSITY", "ACADEMIC_UNIVERSITY_ID", ["ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => 'E']),
            'academicProgram' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_PROGRAMS", "ACADEMIC_PROGRAM_ID", ["ACADEMIC_PROGRAM_NAME"], ["STATUS" => 'E']),
            'academicCourse' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_COURSES", "ACADEMIC_COURSE_ID", ["ACADEMIC_COURSE_NAME"], ["STATUS" => 'E']),
            'rankTypes' => $rankTypes
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (0 === $id) {
            return $this->redirect()->toRoute('employee', ['action' => 'index']);
        }

        $tab = (int) $this->params()->fromRoute('tab', 0);
        if (0 === $tab) {
            return $this->redirect()->toRoute('employee', ['action' => 'index']);
        }


        $this->initializeForm();
        $request = $this->getRequest();

        $formOneModel = new HrEmployeesFormTabOne();
        $formTwoModel = new HrEmployeesFormTabTwo();
        $formThreeModel = new HrEmployeesFormTabThree();
        $formFourModel = new HrEmployeesFormTabFour();
        $formSixModel = new HrEmployeesFormTabSix();

        $employeeData = (array) $this->repository->fetchById($id);
        $profilePictureId = $employeeData[\Setup\Model\HrEmployees::PROFILE_PICTURE_ID];

        if ($request->isPost()) {
            $postData = $request->getPost();
            switch ($tab) {
                case 1:
                    $this->formOne->setData($postData);
                    if ($this->formOne->isValid()) {
                        $formOneModel->exchangeArrayFromForm($this->formOne->getData());
                        $formOneModel->birthDate = Helper::getExpressionDate($formOneModel->birthDate);
                        $formOneModel->addrPermCountryId = 168;
                        $formOneModel->addrTempCountryId = 168;
                        $this->repository->edit($formOneModel, $id);
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 2]);
                    }
                    break;
                case 2:
                    $this->formTwo->setData($postData);
                    if ($this->formTwo->isValid()) {
                        $formTwoModel->exchangeArrayFromForm($this->formTwo->getData());
                        $formTwoModel->famSpouseBirthDate = Helper::getExpressionDate($formTwoModel->famSpouseBirthDate);
                        $formTwoModel->famSpouseWeddingAnniversary = Helper::getExpressionDate($formTwoModel->famSpouseWeddingAnniversary);
                        ;
                        $this->repository->edit($formTwoModel, $id);
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 3]);
                    }
                    break;
                case 3:
                    $this->formThree->setData($postData);
                    if ($this->formThree->isValid()) {
                        $formThreeModel->exchangeArrayFromForm($this->formThree->getData());
                        $formThreeModel->idDrivingLicenseExpiry = Helper::getExpressionDate($formThreeModel->idDrivingLicenseExpiry);
                        $formThreeModel->idCitizenshipIssueDate = Helper::getExpressionDate($formThreeModel->idCitizenshipIssueDate);
                        $formThreeModel->idPassportExpiry = Helper::getExpressionDate($formThreeModel->idPassportExpiry);
                        $this->repository->edit($formThreeModel, $id);
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 4]);
                    }
                    break;
                case 4:
                    $this->formFour->setData($postData);
                    if ($this->formFour->isValid()) {
                        $formFourModel->exchangeArrayFromForm($this->formFour->getData());
                        $formFourModel->branchId = $formFourModel->appBranchId;
                        $formFourModel->departmentId = $formFourModel->appDepartmentId;
                        $formFourModel->designationId = $formFourModel->appDesignationId;
                        $formFourModel->positionId = $formFourModel->appPositionId;
                        $formFourModel->serviceTypeId = $formFourModel->appServiceTypeId;
                        $formFourModel->serviceEventTypeId = $formFourModel->appServiceEventTypeId;
                        $this->repository->edit($formFourModel, $id);
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 5]);
                    }
                    break;
                case 5:
                    break;

                case 6:
                    $this->formSix->setData($postData);

                    if ($this->formSix->isValid()) {
                        
                    }
                    break;
            }
        }
        if ($tab != 1 || !$request->isPost()) {
            $formOneModel->exchangeArrayFromDB($employeeData);
            $this->formOne->bind($formOneModel);
        }

        if ($tab != 2 || !$request->isPost()) {
            $formTwoModel->exchangeArrayFromDB($employeeData);
            $this->formTwo->bind($formTwoModel);
        }

        if ($tab != 3 || !$request->isPost()) {
            $formThreeModel->exchangeArrayFromDB($employeeData);
            $this->formThree->bind($formThreeModel);
        }

        if ($tab != 4 || !$request->isPost()) {
            $formFourModel->exchangeArrayFromDB($employeeData);
            $this->formFour->bind($formFourModel);
        }
        if ($tab != 5 || !$request->isPost()) {
            
        }
        if ($tab != 6 || !$request->isPost()) {
            $formSixModel->exchangeArrayFromDB($employeeData);
            $this->formSix->bind($formSixModel);
        }

        $rankTypes = array(
            'GPA' => "GPA",
            'PER' => 'Percentage'
        );

        return Helper::addFlashMessagesToArray($this, [
                    'formOne' => $this->formOne,
                    'formTwo' => $this->formTwo,
                    'formThree' => $this->formThree,
                    'formFour' => $this->formFour,
                    'formSix' => $this->formSix,
                    'tab' => $tab,
                    "id" => $id,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BLOOD_GROUPS),
                    "districts" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS),
                    "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
                    "vdcMunicipalities" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY),
                    "zones" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_ZONES),
                    "religions" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_RELIGIONS),
                    "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY),
                    "countries" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COUNTRIES),
                    'filetypes' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_FILE_TYPE),
                    'serviceTypes' => ApplicationHelper::getTableKVList($this->adapter, "HR_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E']),
                    'positions' => ApplicationHelper::getTableKVList($this->adapter, "HR_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E']),
                    'designations' => ApplicationHelper::getTableKVList($this->adapter, "HR_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E']),
                    'departments' => ApplicationHelper::getTableKVList($this->adapter, "HR_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E']),
                    'branches' => ApplicationHelper::getTableKVList($this->adapter, "HR_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E']),
                    'serviceEventTypes' => ApplicationHelper::getTableKVList($this->adapter, "HR_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E']),
                    'academicDegree' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_DEGREES", "ACADEMIC_DEGREE_ID", ["ACADEMIC_DEGREE_NAME"], ["STATUS" => 'E']),
                    'academicUniversity' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_UNIVERSITY", "ACADEMIC_UNIVERSITY_ID", ["ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => 'E']),
                    'academicProgram' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_PROGRAMS", "ACADEMIC_PROGRAM_ID", ["ACADEMIC_PROGRAM_NAME"], ["STATUS" => 'E']),
                    'academicCourse' => ApplicationHelper::getTableKVList($this->adapter, "HR_ACADEMIC_COURSES", "ACADEMIC_COURSE_ID", ["ACADEMIC_COURSE_NAME"], ["STATUS" => 'E']),
                    'rankTypes' => $rankTypes,
                    'profilePictureId' => $profilePictureId
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");  
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
        return $this->redirect()->toRoute('employee');
    }

}
