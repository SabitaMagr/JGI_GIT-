<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 11:11 AM
 */

namespace SelfService\Controller;

use Application\Helper\EntityHelper as ApplicationHelper;
use Application\Helper\Helper;
use Setup\Form\HrEmployeesFormTabFive;
use Setup\Form\HrEmployeesFormTabFour;
use Setup\Form\HrEmployeesFormTabOne;
use Setup\Form\HrEmployeesFormTabSix;
use Setup\Form\HrEmployeesFormTabThree;
use Setup\Form\HrEmployeesFormTabTwo;
use Setup\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\EmployeeFile as EmployeeFileModel;
use Setup\Repository\EmployeeQualificationRepository;

class Profile extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;
    private $employeeFileRepo;
    private $formOne;
    private $formTwo;
    private $formThree;
    private $formFour;
    private $formSix;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new EmployeeRepository($adapter);
        $this->employeeFileRepo = new EmployeeFile($this->adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->employeeId = $recordDetail['employee_id'];
    }

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
    public function indexAction() {
       $id = $this->employeeId;
        $tab = (int) $this->params()->fromRoute('tab', 0);
        if ($tab === 0) {
            $tab = 1;
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $empQualificationRepo =  new EmployeeQualificationRepository($this->adapter);

        $formOneModel = new HrEmployeesFormTabOne();
        $formTwoModel = new HrEmployeesFormTabTwo();
        $formThreeModel = new HrEmployeesFormTabThree();
        $formFourModel = new HrEmployeesFormTabFour();
        $formSixModel = new HrEmployeesFormTabSix();

        $employeeData = (array) $this->repository->getById($id);
        $profilePictureId = $employeeData[HrEmployees::PROFILE_PICTURE_ID];
        $filePath = ApplicationHelper::getTableKVList($this->adapter, EmployeeFileModel::TABLE_NAME, EmployeeFileModel::FILE_CODE, [EmployeeFileModel::FILE_PATH], [EmployeeFileModel::FILE_CODE => $profilePictureId], null)[$profilePictureId];
        
        $perVdcMunicipalityDtl = $this->repository->getVdcMunicipalityDtl($employeeData[HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID]);       
        $perDistrictDtl = $this->repository->getDistrictDtl($perVdcMunicipalityDtl['DISTRICT_ID']);       
        $perZoneDtl = $this->repository->getZoneDtl($perDistrictDtl['ZONE_ID']);
        
        $tempVdcMunicipalityDtl = $this->repository->getVdcMunicipalityDtl($employeeData[HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID]);       
        $tempDistrictDtl = $this->repository->getDistrictDtl($tempVdcMunicipalityDtl['DISTRICT_ID']);       
        $tempZoneDtl = $this->repository->getZoneDtl($tempDistrictDtl['ZONE_ID']);
        
        $empQualificationDtl = $empQualificationRepo->getByEmpId($id);
 
        return Helper::addFlashMessagesToArray($this, [
                    'formOne' => $this->formOne,
                    'formTwo' => $this->formTwo,
                    'formThree' => $this->formThree,
                    'formFour' => $this->formFour,
                    'formSix' => $this->formSix,
                    "id" => $id,
                    'profilePictureId' => $profilePictureId,
                    'employeeData'=>$employeeData,
                    'filePath'=>$filePath,
                    'perDistrictName'=>$perDistrictDtl['DISTRICT_NAME'],
                    'perZoneName'=>$perZoneDtl['ZONE_NAME'],
                    'tempDistrictName'=>$tempDistrictDtl['DISTRICT_NAME'],
                    'tempZoneName'=>$tempZoneDtl['ZONE_NAME'],
                    'empQualificationList'=>$empQualificationDtl
        ]);
    }

    public function profileAction() {
        $id = $this->employeeId;
        $tab = (int) $this->params()->fromRoute('tab', 0);
        if ($tab === 0) {
            $tab = 1;
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
        
        ///print_r($employeeData); die();

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
                        return $this->redirect()->toRoute('profile', ['tab' => 2]);
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
                        return $this->redirect()->toRoute('profile', ['action' => 'index', 'tab' => 3]);
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
                        return $this->redirect()->toRoute('profile', ['action' => 'index', 'tab' => 4]);
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
                        return $this->redirect()->toRoute('profile', ['action' => 'index', 'tab' => 5]);
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

}
