<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 11:02 AM
 */

namespace Setup\Controller;

use Application\Helper\EntityHelper as ApplicationHelper;
use Application\Helper\EntityHelper as EntityHelper2;
use Application\Helper\Helper;
use Setup\Form\HrEmployeesFormTabFive;
use Setup\Form\HrEmployeesFormTabFour;
use Setup\Form\HrEmployeesFormTabOne;
use Setup\Form\HrEmployeesFormTabSix;
use Setup\Form\HrEmployeesFormTabThree;
use Setup\Form\HrEmployeesFormTabTwo;
use Setup\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\District;
use Setup\Model\HrEmployees;
use Setup\Model\JobHistory;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Model\VdcMunicipalities;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\JobHistoryRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmployeeController extends AbstractActionController {

    private $adapter;
    private $form;
    private $repository;
    private $employeeFileRepo;
    private $jobHistoryRepo;

    const UPLOAD_DIR = "/var/www/html/neo/neo-hris-metronic/public/uploads";

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new EmployeeRepository($adapter);
        $this->employeeFileRepo = new EmployeeFile($this->adapter);
        $this->jobHistoryRepo = new JobHistoryRepository($this->adapter);
    }

    public function indexAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
<<<<<<< HEAD
        $employeeName = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
=======
        $employeeName = EntityHelper2::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], " ");
        $employeeName[-1] = "All";
        ksort($employeeName);
        $employeeNameFormElement->setValueOptions($employeeName);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
<<<<<<< HEAD
        $branches = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
=======
        $branches = EntityHelper2::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E']);
        $branches[-1] = "All";
        ksort($branches);
        $branchFormElement->setValueOptions($branches);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
<<<<<<< HEAD
        $departments = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
=======
        $departments = EntityHelper2::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E']);
        $departments[-1] = "All";
        ksort($departments);
        $departmentFormElement->setValueOptions($departments);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
<<<<<<< HEAD
        $designations = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
=======
        $designations = EntityHelper2::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E']);
        $designations[-1] = "All";
        ksort($designations);
        $designationFormElement->setValueOptions($designations);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
<<<<<<< HEAD
        $positions = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC");
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
=======
        $positions = EntityHelper2::getTableKVList($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E']);
        $positions[-1] = "All";
        ksort($positions);
        $positionFormElement->setValueOptions($positions);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
<<<<<<< HEAD
        $serviceTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
=======
        $serviceTypes = EntityHelper2::getTableKVList($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E']);
        $serviceTypes[-1] = "All";
        ksort($serviceTypes);
        $serviceTypeFormElement->setValueOptions($serviceTypes);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
<<<<<<< HEAD
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC");
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
=======
        $serviceEventTypes = EntityHelper2::getTableKVList($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E']);
        $serviceEventTypes[-1] = "All";
        ksort($serviceEventTypes);
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes);
>>>>>>> bef43404d1599dc0033d774927154fa16436e046
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
            'serviceTypes' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'], "SERVICE_TYPE_NAME", "ASC", null, true),
            'positions' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], "POSITION_NAME", "ASC", null, true),
            'designations' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], "DESIGNATION_TITLE", "ASC", null, true),
            'departments' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, true),
            'branches' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], "BRANCH_NAME", "ASC", null, true),
            'serviceEventTypes' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC", null, true),
            'academicDegree' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_DEGREES", "ACADEMIC_DEGREE_ID", ["ACADEMIC_DEGREE_NAME"], ["STATUS" => 'E'], "ACADEMIC_DEGREE_NAME", "ASC"),
            'academicUniversity' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_UNIVERSITY", "ACADEMIC_UNIVERSITY_ID", ["ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => 'E'], "ACADEMIC_UNIVERSITY_NAME", "ASC"),
            'academicProgram' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_PROGRAMS", "ACADEMIC_PROGRAM_ID", ["ACADEMIC_PROGRAM_NAME"], ["STATUS" => 'E'], "ACADEMIC_PROGRAM_NAME", "ASC"),
            'academicCourse' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_COURSES", "ACADEMIC_COURSE_ID", ["ACADEMIC_COURSE_NAME"], ["STATUS" => 'E'], "ACADEMIC_COURSE_NAME", "ASC"),
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
        $profilePictureId = $employeeData[HrEmployees::PROFILE_PICTURE_ID];
        $address = [];
        $getJobHistoryByEmployeeId = $this->jobHistoryRepo->filter(null, null, $id, -1);
        $empJobHistoryList = [];
        foreach ($getJobHistoryByEmployeeId as $row) {
            array_push($empJobHistoryList, $row);
        }
        $jobHistoryListNum = count($empJobHistoryList);

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
                    $jobHistoryModel = new JobHistory();
                    if ($this->formFour->isValid()) {
                        $formFourModel->exchangeArrayFromForm($this->formFour->getData());
                        $formFourModel->branchId = $formFourModel->appBranchId;
                        $formFourModel->departmentId = $formFourModel->appDepartmentId;
                        $formFourModel->designationId = $formFourModel->appDesignationId;
                        $formFourModel->positionId = $formFourModel->appPositionId;
                        $formFourModel->serviceTypeId = $formFourModel->appServiceTypeId;
                        $formFourModel->serviceEventTypeId = $formFourModel->appServiceEventTypeId;
                        $this->repository->edit($formFourModel, $id);

                        if ($jobHistoryListNum == 0) {
                            if ($formFourModel->appBranchId != null && $formFourModel->appDepartmentId != null && $formFourModel->appDesignationId != null && $formFourModel->appPositionId != null && $formFourModel->appServiceTypeId != null) {
                                $jobHistoryModel->jobHistoryId = (int) Helper::getMaxId($this->adapter, $jobHistoryModel::TABLE_NAME, $jobHistoryModel::JOB_HISTORY_ID) + 1;
                                $jobHistoryModel->employeeId = $id;
                                $jobHistoryModel->startDate = Helper::getExpressionDate($formFourModel->joinDate);
                                $jobHistoryModel->serviceEventTypeId = $formFourModel->appServiceEventTypeId;
                                $jobHistoryModel->fromBranchId = $formFourModel->appBranchId;
                                $jobHistoryModel->fromDepartmentId = $formFourModel->appDepartmentId;
                                $jobHistoryModel->fromDesignationId = $formFourModel->appDesignationId;
                                $jobHistoryModel->fromPositionId = $formFourModel->appPositionId;
                                $jobHistoryModel->fromServiceTypeId = $formFourModel->appServiceTypeId;
                                $jobHistoryModel->toBranchId = $formFourModel->appBranchId;
                                $jobHistoryModel->toDepartmentId = $formFourModel->appDepartmentId;
                                $jobHistoryModel->toDesignationId = $formFourModel->appDesignationId;
                                $jobHistoryModel->toPositionId = $formFourModel->appPositionId;
                                $jobHistoryModel->toServiceTypeId = $formFourModel->appServiceTypeId;
                                $jobHistoryModel->status = 'E';
                                $this->jobHistoryRepo->add($jobHistoryModel);
                            }
                        }

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

            if (isset($formOneModel->addrPermVdcMunicipalityId)) {
                $address['addrPermVdcMunicipalityId'] = $formOneModel->addrPermVdcMunicipalityId;
                $tempArray = ApplicationHelper::getTableKVList($this->adapter, VdcMunicipalities::TABLE_NAME, null, [VdcMunicipalities::DISTRICT_ID], [VdcMunicipalities::VDC_MUNICIPALITY_ID => $formOneModel->addrPermVdcMunicipalityId], null);
                if (isset($tempArray) && (sizeof($tempArray) > 0)) {
                    $formOneModel->addrPermDistrictId = $tempArray[0];
                    $address["addrPermDistrictId"] = $tempArray[0];
                }
            }
            if (isset($formOneModel->addrPermDistrictId)) {
                $tempArray = ApplicationHelper::getTableKVList($this->adapter, District::TABLE_NAME, null, [District::ZONE_ID], [District::DISTRICT_ID => $formOneModel->addrPermDistrictId], null);
                if (isset($tempArray) && (sizeof($tempArray) > 0)) {
                    $formOneModel->addrPermZoneId = $tempArray[0];
                    $address["addrPermZoneId"] = $tempArray[0];
                }
            }
            if (isset($formOneModel->addrTempVdcMunicipalityId)) {
                $address['addrTempVdcMunicipalityId'] = $formOneModel->addrTempVdcMunicipalityId;
                $tempArray = ApplicationHelper::getTableKVList($this->adapter, VdcMunicipalities::TABLE_NAME, null, [VdcMunicipalities::DISTRICT_ID], [VdcMunicipalities::VDC_MUNICIPALITY_ID => $formOneModel->addrTempVdcMunicipalityId], null);
                if (isset($tempArray) && (sizeof($tempArray) > 0)) {
                    $formOneModel->addrTempDistrictId = $tempArray[0];
                    $address["addrTempDistrictId"] = $tempArray[0];
                }
            }
            if (isset($formOneModel->addrTempDistrictId)) {
                $tempArray = ApplicationHelper::getTableKVList($this->adapter, District::TABLE_NAME, null, [District::ZONE_ID], [District::DISTRICT_ID => $formOneModel->addrTempDistrictId], null);
                if (isset($tempArray) && (sizeof($tempArray) > 0)) {
                    $formOneModel->addrTempZoneId = $tempArray[0];
                    $address["addrTempZoneId"] = $tempArray[0];
                }
            }
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
                    "jobHistoryListNum" => $jobHistoryListNum,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BLOOD_GROUPS),
                    "districts" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS),
                    "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
                    "vdcMunicipalities" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY),
                    "zones" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_ZONES),
                    "religions" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_RELIGIONS),
                    "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY),
                    "countries" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COUNTRIES),
                    'filetypes' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_FILE_TYPE),
                    'serviceTypes' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'], "SERVICE_TYPE_NAME", "ASC", null, true),
                    'positions' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], "POSITION_NAME", "ASC", null, true),
                    'designations' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], "DESIGNATION_TITLE", "ASC", null, true),
                    'departments' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, true),
                    'branches' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], "BRANCH_NAME", "ASC", null, true),
                    'serviceEventTypes' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC", null, true),
                    'academicDegree' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_DEGREES", "ACADEMIC_DEGREE_ID", ["ACADEMIC_DEGREE_NAME"], ["STATUS" => 'E'], "ACADEMIC_DEGREE_NAME", "ASC"),
                    'academicUniversity' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_UNIVERSITY", "ACADEMIC_UNIVERSITY_ID", ["ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => 'E'], "ACADEMIC_UNIVERSITY_NAME", "ASC"),
                    'academicProgram' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_PROGRAMS", "ACADEMIC_PROGRAM_ID", ["ACADEMIC_PROGRAM_NAME"], ["STATUS" => 'E'], "ACADEMIC_PROGRAM_NAME", "ASC"),
                    'academicCourse' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HR_ACADEMIC_COURSES", "ACADEMIC_COURSE_ID", ["ACADEMIC_COURSE_NAME"], ["STATUS" => 'E'], "ACADEMIC_COURSE_NAME", "ASC"),
                    'rankTypes' => $rankTypes,
                    'profilePictureId' => $profilePictureId,
                    'address' => $address,
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
        return $this->redirect()->toRoute('employee');
    }

}
