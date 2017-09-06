<?php

namespace Setup\Controller;

use Application\Factory\ConfigInterface;
use Application\Helper\EntityHelper as ApplicationHelper;
use Application\Helper\Helper;
use Asset\Repository\IssueRepository;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\ShiftAssignRepository;
use LeaveManagement\Model\LeaveMaster;
use Setup\Form\HrEmployeesFormTabEight;
use Setup\Form\HrEmployeesFormTabFive;
use Setup\Form\HrEmployeesFormTabFour;
use Setup\Form\HrEmployeesFormTabOne;
use Setup\Form\HrEmployeesFormTabSeven;
use Setup\Form\HrEmployeesFormTabSix;
use Setup\Form\HrEmployeesFormTabThree;
use Setup\Form\HrEmployeesFormTabTwo;
use Setup\Model\EmployeeFile as EmployeeFileModel;
use Setup\Model\HrEmployees;
use Setup\Model\JobHistory;
use Setup\Model\RecommendApprove;
use Setup\Repository\EmployeeExperienceRepository;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeQualificationRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\EmployeeTrainingRepository;
use Setup\Repository\JobHistoryRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmployeeController extends AbstractActionController {

    private $adapter;
    private $config;
    private $form;
    private $repository;
    private $employeeFileRepo;
    private $jobHistoryRepo;
    private $formOne;
    private $formTwo;
    private $formThree;
    private $formFour;
    private $formSix;
    private $formSeven;
    private $formEight;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->repository = new EmployeeRepository($adapter);
        $this->employeeFileRepo = new EmployeeFile($this->adapter);
        $this->jobHistoryRepo = new JobHistoryRepository($this->adapter);
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => ApplicationHelper::getSearchData($this->adapter)
        ]);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $formTabOne = new HrEmployeesFormTabOne();
        $formTabTwo = new HrEmployeesFormTabTwo();
        $formTabThree = new HrEmployeesFormTabThree();
        $formTabFour = new HrEmployeesFormTabFour();
        $formTabFive = new HrEmployeesFormTabFive();
        $formTabSix = new HrEmployeesFormTabSix();
        $formTabSeven = new HrEmployeesFormTabSeven();
        $formTabEight = new HrEmployeesFormTabEight();

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
        if (!$this->formSeven) {
            $this->formSeven = $builder->createForm($formTabSeven);
        }
        if (!$this->formEight) {
            $this->formEight = $builder->createForm($formTabEight);
        }
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $tab = (int) $this->params()->fromRoute('tab', 1);

        if (10 === $tab) {
            $this->flashmessenger()->addMessage("Employee Successfully Submitted!!!");
            return $this->redirect()->toRoute('employee', ['action' => 'index']);
        }


        $this->initializeForm();
        $request = $this->getRequest();

        $formOneModel = new HrEmployeesFormTabOne();
        $formTwoModel = new HrEmployeesFormTabTwo();
        $formThreeModel = new HrEmployeesFormTabThree();
        $formFourModel = new HrEmployeesFormTabFour();
        $formSixModel = new HrEmployeesFormTabSix();

        $shiftAssignRepo = new ShiftAssignRepository($this->adapter);
        $recommApproverRepo = new RecommendApproveRepository($this->adapter);
        $employeeData = null;
        $profilePictureId = null;
        $getEmpShiftDtl = null;
        $employeePreDtl = null;
        if ($id != 0) {
            $employeeData = (array) $this->repository->fetchById($id);
            $profilePictureId = $employeeData[HrEmployees::PROFILE_PICTURE_ID];
            $getEmpShiftDtl = $shiftAssignRepo->fetchByEmployeeId($id);
            $employeePreDtl = $recommApproverRepo->fetchById($id);
        }
        $address = [];
        if ($request->isPost()) {
            $postData = $request->getPost();
            switch ($tab) {
                case 1:
                    $this->formOne->setData($postData);
                    if ($this->formOne->isValid()) {

                        $formOneModel->exchangeArrayFromForm($this->formOne->getData());
                        $formOneModel->addrPermVdcMunicipalityId = $this->repository->vdcStringToId($postData['addrPermDistrictId'], $postData['addrPermVdcMunicipalityId']);
                        $formOneModel->addrTempVdcMunicipalityId = $this->repository->vdcStringToId($postData['addrTempDistrictId'], $postData['addrTempVdcMunicipalityId']);
                        $formOneModel->birthDate = Helper::getExpressionDate($formOneModel->birthDate);
                        $formOneModel->addrPermCountryId = 168;
                        $formOneModel->addrTempCountryId = 168;

                        if ($id == 0) {
                            $id = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID")) + 1;
                            $formOneModel->employeeId = $id;
                            $formOneModel->createdBy = $this->loggedIdEmployeeId;
                            $formOneModel->createdDt = Helper::getcurrentExpressionDate();
                            $this->repository->add($formOneModel);
                        } else {
                            $formOneModel->modifiedBy = $this->loggedIdEmployeeId;
                            $formOneModel->modifiedDt = Helper::getcurrentExpressionDate();
                            $this->repository->edit($formOneModel, $id);
                        }

                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 2]);
                    }
                    break;
                case 2:
                    $this->formTwo->setData($postData);
                    if ($this->formTwo->isValid()) {
                        $formTwoModel->exchangeArrayFromForm($this->formTwo->getData());
                        $formTwoModel->famSpouseBirthDate = Helper::getExpressionDate($formTwoModel->famSpouseBirthDate);
                        $formTwoModel->famSpouseWeddingAnniversary = Helper::getExpressionDate($formTwoModel->famSpouseWeddingAnniversary);

                        $formTwoModel->modifiedBy = $this->loggedIdEmployeeId;
                        $formTwoModel->modifiedDt = Helper::getcurrentExpressionDate();
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
                        $formThreeModel->modifiedBy = $this->loggedIdEmployeeId;
                        $formThreeModel->modifiedDt = Helper::getcurrentExpressionDate();
                        $this->repository->edit($formThreeModel, $id);
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 4]);
                    }
                    break;
                case 4:
                    $this->formFour->setData($postData);
                    if ($this->formFour->isValid()) {
                        $formFourModel->exchangeArrayFromForm($this->formFour->getData());
                        $formFourModel->modifiedBy = $this->loggedIdEmployeeId;
                        $formFourModel->modifiedDt = Helper::getcurrentExpressionDate();
                        $this->repository->edit($formFourModel, $id);
                        /*
                         * Shift Assign part
                         */
                        $shiftId = $postData->shift;
                        $shiftAssign = new ShiftAssign();
                        $shiftAssign->employeeId = $id;
                        $shiftAssign->shiftId = $shiftId;

                        if ($getEmpShiftDtl != null) {
                            $shiftAssignClone = clone $shiftAssign;

                            unset($shiftAssignClone->employeeId);
                            unset($shiftAssignClone->shiftId);
                            unset($shiftAssignClone->createdDt);

                            if ($shiftId != $getEmpShiftDtl['SHIFT_ID']) {
                                $shiftAssignClone->status = 'D';
                                $shiftAssignClone->modifiedDt = Helper::getcurrentExpressionDate();
                                $shiftAssignClone->modifiedBy = $this->loggedIdEmployeeId;
                                $shiftAssignRepo->edit($shiftAssignClone, [$id, $getEmpShiftDtl['SHIFT_ID']]);

                                $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
                                $shiftAssign->createdBy = $this->loggedIdEmployeeId;
                                $shiftAssign->status = 'E';
                                $shiftAssignRepo->add($shiftAssign);
                            }
                        } else {
                            $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
                            $shiftAssign->createdBy = $this->loggedIdEmployeeId;
                            $shiftAssign->status = 'E';
                            $shiftAssignRepo->add($shiftAssign);
                        }
                        /*
                         * 
                         */

                        /*
                         * Recommender Approver Assign part 
                         */
                        $recommenderId = $postData->recommender;
                        $approverId = $postData->approver;

                        $recommendApprove = new RecommendApprove();
                        if ($employeePreDtl == null) {
                            $recommendApprove->employeeId = $id;
                            $recommendApprove->recommendBy = $recommenderId;
                            $recommendApprove->approvedBy = $approverId;
                            $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
                            $recommendApprove->status = 'E';
                            $recommApproverRepo->add($recommendApprove);
                        } else if ($employeePreDtl != null) {
                            $id = $employeePreDtl['EMPLOYEE_ID'];
                            $recommendApprove->employeeId = $id;
                            $recommendApprove->recommendBy = $recommenderId;
                            $recommendApprove->approvedBy = $approverId;
                            $recommendApprove->modifiedDt = Helper::getcurrentExpressionDate();
                            $recommendApprove->status = 'E';
                            $recommApproverRepo->edit($recommendApprove, $id);
                        }
                        /*
                         * 
                         */
                        return $this->redirect()->toRoute('employee', ['action' => 'edit', 'id' => $id, 'tab' => 5]);
                    }
                    break;
                case 5:
                    break;
                case 6:
                    break;
                case 7:
                    break;
                case 8:
                    break;
                case 9:
                    break;
            }
        }
        if ($employeeData != null) {
            if ($tab != 1 || !$request->isPost()) {
                $formOneModel->exchangeArrayFromDB($employeeData);
                $address['addrPermZoneId'] = $formOneModel->addrPermZoneId;
                $address['addrPermDistrictId'] = $formOneModel->addrPermDistrictId;
                $address['addrPermVdcMunicipalityId'] = $formOneModel->addrPermVdcMunicipalityId;
                $address['addrTempZoneId'] = $formOneModel->addrTempZoneId;
                $address['addrTempDistrictId'] = $formOneModel->addrTempDistrictId;
                $address['addrTempVdcMunicipalityId'] = $formOneModel->addrTempVdcMunicipalityId;

                $formOneModel->addrPermVdcMunicipalityId = $this->repository->vdcIdToString($formOneModel->addrPermVdcMunicipalityId);
                $formOneModel->addrTempVdcMunicipalityId = $this->repository->vdcIdToString($formOneModel->addrTempVdcMunicipalityId);
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

            if ($tab != 7 || !$request->isPost()) {
                
            }

            if ($tab != 8 || !$request->isPost()) {
                
            }
        }
        $rankTypes = array(
            'GPA' => "GPA",
            'PER' => 'Percentage'
        );

        return Helper::addFlashMessagesToArray($this, [
                    'tab' => $tab,
                    "id" => $id,
                    'formOne' => $this->formOne,
                    'formTwo' => $this->formTwo,
                    'formThree' => $this->formThree,
                    'formFour' => $this->formFour,
                    'formSix' => $this->formSix,
                    'formSeven' => $this->formSeven,
                    'formEight' => $this->formEight,
                    "bloodGroups" => ApplicationHelper::getTableKVList($this->adapter, 'HRIS_BLOOD_GROUPS', 'BLOOD_GROUP_ID', ['BLOOD_GROUP_CODE'], NULL, NULL, TRUE),
                    "genders" => ApplicationHelper::getTableKVList($this->adapter, \Setup\Model\Gender::TABLE_NAME, \Setup\Model\Gender::GENDER_ID, [\Setup\Model\Gender::GENDER_NAME], null, null, true),
                    "zones" => ApplicationHelper::getTableKVList($this->adapter, \Setup\Model\Zones::TABLE_NAME, \Setup\Model\Zones::ZONE_ID, [\Setup\Model\Zones::ZONE_NAME], null, null, true),
                    "religions" => ApplicationHelper::getTableKVList($this->adapter, 'HRIS_RELIGIONS', 'RELIGION_ID', ['RELIGION_NAME'], null, null, true),
                    "companies" => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_COMPANY", "COMPANY_ID", ["COMPANY_NAME"], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, false, true),
                    "countries" => ApplicationHelper::getTableKVList($this->adapter, 'HRIS_COUNTRIES', 'COUNTRY_ID', ['COUNTRY_NAME'], null, null, true),
                    'filetypes' => ApplicationHelper::getTableKVList($this->adapter, 'HRIS_FILE_TYPE', 'FILETYPE_CODE', ['NAME']),
                    'serviceTypes' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'], "SERVICE_TYPE_NAME", "ASC", null, true, true),
                    'positions' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], "POSITION_NAME", "ASC", null, true, true),
                    'designations' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], "DESIGNATION_TITLE", "ASC", null, true, true),
                    'departments' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, true, true),
                    'branches' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], "BRANCH_NAME", "ASC", null, true, true),
                    'academicDegree' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ACADEMIC_DEGREES", "ACADEMIC_DEGREE_ID", ["ACADEMIC_DEGREE_NAME"], ["STATUS" => 'E'], "ACADEMIC_DEGREE_NAME", "ASC", null, false, true),
                    'academicUniversity' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ACADEMIC_UNIVERSITY", "ACADEMIC_UNIVERSITY_ID", ["ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => 'E'], "ACADEMIC_UNIVERSITY_NAME", "ASC", null, false, true),
                    'academicProgram' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ACADEMIC_PROGRAMS", "ACADEMIC_PROGRAM_ID", ["ACADEMIC_PROGRAM_NAME"], ["STATUS" => 'E'], "ACADEMIC_PROGRAM_NAME", "ASC", null, false, true),
                    'academicCourse' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ACADEMIC_COURSES", "ACADEMIC_COURSE_ID", ["ACADEMIC_COURSE_NAME"], ["STATUS" => 'E'], "ACADEMIC_COURSE_NAME", "ASC", null, false, true),
                    'rankTypes' => $rankTypes,
                    'profilePictureId' => $profilePictureId,
                    'address' => $address,
                    'shiftId' => ($getEmpShiftDtl != null) ? $getEmpShiftDtl['SHIFT_ID'] : 0,
                    'recommenderId' => ($employeePreDtl != null) ? $employeePreDtl['RECOMMEND_BY'] : 0,
                    'approverId' => ($employeePreDtl != null) ? $employeePreDtl['APPROVED_BY'] : 0,
                    'shifts' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, ShiftSetup::TABLE_NAME, ShiftSetup::SHIFT_ID, [ShiftSetup::SHIFT_ENAME], [ShiftSetup::STATUS => 'E'], ShiftSetup::SHIFT_ENAME, "ASC", null, false, true),
                    'leaves' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E'], LeaveMaster::LEAVE_ENAME, "ASC", null, false, true),
                    'recommenders' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ", false, true),
                    'approvers' => ApplicationHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ", false, true),
                    'customRender' => Helper::renderCustomView()
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if (0 === $id) {
            return $this->redirect()->toRoute('employee', ['action' => 'index']);
        }

        $this->initializeForm();
        $empQualificationRepo = new EmployeeQualificationRepository($this->adapter);
        $empExperienceRepo = new EmployeeExperienceRepository($this->adapter);
        $empTrainingRepo = new EmployeeTrainingRepository($this->adapter);

        $employeeData = (array) $this->repository->getById($id);
        $profilePictureId = $employeeData[HrEmployees::PROFILE_PICTURE_ID];
        $filePathArray = ApplicationHelper::getTableKVList($this->adapter, EmployeeFileModel::TABLE_NAME, null, [EmployeeFileModel::FILE_PATH], [EmployeeFileModel::FILE_CODE => $profilePictureId], null);
        $filePath = empty($filePathArray) ? $this->config->getApplicationConfig()['default-profile-picture'] : $filePathArray[0];

        $perVdcMunicipalityDtl = $this->repository->getVdcMunicipalityDtl($employeeData[HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID]);
        $perDistrictDtl = $this->repository->getDistrictDtl($perVdcMunicipalityDtl['DISTRICT_ID']);
        $perZoneDtl = $this->repository->getZoneDtl($perDistrictDtl['ZONE_ID']);

        $tempVdcMunicipalityDtl = $this->repository->getVdcMunicipalityDtl($employeeData[HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID]);
        $tempDistrictDtl = $this->repository->getDistrictDtl($tempVdcMunicipalityDtl['DISTRICT_ID']);
        $tempZoneDtl = $this->repository->getZoneDtl($tempDistrictDtl['ZONE_ID']);

        $empQualificationDtl = $empQualificationRepo->getByEmpId($id);
        $empExperienceList = $empExperienceRepo->getByEmpId($id);
        $empTrainingList = $empTrainingRepo->getByEmpId($id);

        $jobHistoryRepo = new JobHistoryRepository($this->adapter);
        $jobHistoryList = $jobHistoryRepo->filter(null, null, $id);

        $employeeFileRepo = new EmployeeFile($this->adapter);
        $employeeFile = $employeeFileRepo->fetchByEmpId($id);

        $assetRepo = new IssueRepository($this->adapter);
        $assetDetails = $assetRepo->fetchAssetByEmployee($id);


        return Helper::addFlashMessagesToArray($this, [
                    'formOne' => $this->formOne,
                    'formTwo' => $this->formTwo,
                    'formThree' => $this->formThree,
                    'formFour' => $this->formFour,
                    'formSix' => $this->formSix,
                    "formSeven" => $this->formSeven,
                    'formEight' => $this->formEight,
                    "id" => $id,
                    'profilePictureId' => $profilePictureId,
                    'employeeData' => $employeeData,
                    'filePath' => $filePath,
                    'perDistrictName' => $perDistrictDtl['DISTRICT_NAME'],
                    'perZoneName' => $perZoneDtl['ZONE_NAME'],
                    'tempDistrictName' => $tempDistrictDtl['DISTRICT_NAME'],
                    'tempZoneName' => $tempZoneDtl['ZONE_NAME'],
                    'empQualificationList' => $empQualificationDtl,
                    'empExperienceList' => $empExperienceList,
                    'empTrainingList' => $empTrainingList,
                    'jobHistoryList' => $jobHistoryList,
                    "employeeFile" => $employeeFile,
                    "assetDetails" => $assetDetails
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
        return $this->redirect()->toRoute('employee');
    }

}
