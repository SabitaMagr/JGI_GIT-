<?php

namespace RestfulService\Controller;

use Advance\Repository\AdvanceStatusRepository;
use Application\Helper\ConstraintHelper;
use Application\Helper\DeleteHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\LoanAdvanceHelper;
use Application\Repository\MonthRepository;
use Appraisal\Repository\HeadingRepository;
use Appraisal\Repository\QuestionRepository;
use Asset\Repository\IssueRepository;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceRepository;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use AttendanceManagement\Repository\ShiftAssignRepository;
use DateTime;
use Exception;
use HolidayManagement\Repository\HolidayRepository;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveStatusRepository;
use Loan\Repository\LoanStatusRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Overtime\Repository\OvertimeStatusRepository;
use Payroll\Controller\PayrollGenerator;
use Payroll\Controller\SalarySheet as SalarySheetController;
use Payroll\Controller\VariableProcessor;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Model\PayPositionSetup;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\PayPositionRepo;
use Payroll\Repository\RulesDetailRepo;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use SelfService\Repository\AttendanceRequestRepository;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use SelfService\Repository\ServiceRepository;
use ServiceQuestion\Repository\EmpServiceQuestionDtlRepo;
use Setup\Model\EmployeeExperience;
use Setup\Model\EmployeeQualification;
use Setup\Model\EmployeeTraining;
use Setup\Model\RecommendApprove;
use Setup\Repository\AcademicCourseRepository;
use Setup\Repository\AcademicDegreeRepository;
use Setup\Repository\AcademicProgramRepository;
use Setup\Repository\AcademicUniversityRepository;
use Setup\Repository\AdvanceRepository;
use Setup\Repository\EmployeeExperienceRepository;
use Setup\Repository\EmployeeQualificationRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\EmployeeTrainingRepository;
use Setup\Repository\JobHistoryRepository;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\ServiceQuestionRepository;
use System\Model\DashboardDetail;
use System\Model\MenuSetup;
use System\Model\RolePermission;
use System\Repository\DashboardDetailRepo;
use System\Repository\MenuSetupRepository;
use System\Repository\RolePermissionRepository;
use System\Repository\RoleSetupRepository;
use System\Repository\UserSetupRepository;
use Training\Model\TrainingAssign;
use Training\Repository\TrainingAssignRepository;
use Training\Repository\TrainingStatusRepository;
use Travel\Repository\TravelStatusRepository;
use WorkOnDayoff\Repository\WorkOnDayoffStatusRepository;
use WorkOnHoliday\Repository\WorkOnHolidayStatusRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Application\Repository\ForgotPasswordRepository;
use ServiceQuestion\Repository\EmpServiceQuestionRepo;
use SelfService\Repository\AppraisalKPIRepository;
use SelfService\Model\AppraisalKPI;
use SelfService\Model\AppraisalCompetencies;
use SelfService\Repository\AppraisalCompetenciesRepo;
use Appraisal\Repository\AppraisalAssignRepository;
use Application\Helper\AppraisalHelper;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Repository\AppraisalReportRepository;

class RestfulService extends AbstractRestfulController {

    private $adapter;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function convertResultInterfaceIntoArray(ResultInterface $result) {
        $tempArray = [];
        foreach ($result as $unit) {
            array_push($tempArray, $unit);
        }
        return $tempArray;
    }

    public function indexAction() {
        $request = $this->getRequest();

        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);
                if ($success) {
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                }
            } else if ($request->isPost()) {
                $postedData = $request->getPost();
                if ($postedData == null) {
                    throw new Exception("request is not defined");
                }

                if (!isset($postedData->action) || $postedData->action == null || empty($postedData->action)) {
                    throw new Exception("action not defined");
                }

                switch ($postedData->action) {
                    case "pullEmployeeForShiftAssign":
                        $responseData = $this->pullEmployeeForShiftAssign($postedData->id);
                        break;

                    case "pullEmployeeForRecomApproverAssign":
                        $responseData = $this->pullEmployeeForRecomApproverAssign($postedData->data);
                        break;
                    case "assignEmployeeReportingHierarchy":
                        $responseData = $this->assignEmployeeReportingHierarchy($postedData->data);
                        break;
                    case "assignEmployeeTraining":
                        $responseData = $this->assignEmployeeTraining($postedData->data);
                        break;
                    case "cancelEmployeeTraining":
                        $responseData = $this->cancelEmployeeTraining($postedData->data);
                        break;
                    case "assignEmployeeShift":
                        $responseData = $this->assignEmployeeShift($postedData->data);
                        break;

                    case "pullEmployeeMonthlyValue":
                        $responseData = $this->pullEmployeeMonthlyValue($postedData->id);
                        break;
                    case "pushEmployeeMonthlyValue":
                        $responseData = $this->pushEmployeeMonthlyValue($postedData->id);
                        break;

                    case "pullEmployeeFlatValue":
                        $responseData = $this->pullEmployeeFlatValue($postedData->id);
                        break;
                    case "pushEmployeeFlatValue":
                        $responseData = $this->pushEmployeeFlatValue($postedData->id);
                        break;
                    case "pushRule":
                        $responseData = $this->pushRule($postedData->data);
                        break;
                    case "pullRule":
                        $responseData = $this->pullRule($postedData->data);
                        break;
                    case "pushRuleDetail":
                        $responseData = $this->pushRuleDetail($postedData->data);
                        break;
                    case "pullRuleDetailByPayId":
                        $responseData = $this->pullRuleDetailByPayId($postedData->data);
                        break;
                    case "menu":
                        $responseData = $this->menu();
                        break;
                    case "menuInsertion":
                        $responseData = $this->menuInsertion($postedData->data);
                        break;
                    case "headingList":
                        $responseData = $this->headingList();
                        break;

                    case "menuUpdate":
                        $responseData = $this->menuUpdate($postedData->data);
                        break;
                    case "pullEmployeeListForReportingRole":
                        $responseData = $this->pullEmployeeListForReportingRole($postedData->data);
                        break;
                    case "pullEmployeeForTrainingAssign":
                        $responseData = $this->pullEmployeeForTrainingAssign($postedData->data);
                        break;
                    case "pullTrainingAssignList":
                        $responseData = $this->pullTrainingAssignList($postedData->data);
                        break;
                    case "pullMenuDetail":
                        $responseData = $this->pullMenuDetail($postedData->data);
                        break;
                    case "permissionAssign":
                        $responseData = $this->permissionAssign($postedData->data);
                        break;
                    case "pullRolePermissionList":
                        $responseData = $this->pullRolePermissionList($postedData->data);
                        break;
                    case "pullServiceHistory":
                        $responseData = $this->pullServiceHistory($postedData->data);
                        break;
                    case "pullLeaveBalanceDetail":
                        $responseData = $this->pullLeaveBalanceDetail($postedData->data);
                        break;
                    case "pullHolidayList":
                        $responseData = $this->pullHolidayList($postedData->data);
                        break;
                    case "pullPositionsAssignedByPayId":
                        $responseData = $this->pullPositionsAssignedByPayId($postedData->data);
                        break;
                    case "addPositionAssigned":
                        $responseData = $this->addPositionAssigned($postedData->data);
                        break;
                    case "deletePositionAssigned":
                        $responseData = $this->deletePositionAssigned($postedData->data);
                        break;
                    case "pullAcademicDetail":
                        $responseData = $this->pullAcademicDetail($postedData->data);
                        break;
                    case "pullExperienceDetail":
                        $responseData = $this->pullExperienceDetail($postedData->data);
                        break;
                    case "pullTrainingDetail":
                        $responseData = $this->pullTrainingDetail($postedData->data);
                        break;
                    case "submitQualificationDtl":
                        $responseData = $this->submitQualificationDtl($postedData->data);
                        break;
                    case "submitExperienceDtl":
                        $responseData = $this->submitExperienceDtl($postedData->data);
                        break;
                    case "submitTrainingDtl":
                        $responseData = $this->submitTrainingDtl($postedData->data);
                        break;
                    case "deleteQualificationDtl":
                        $responseData = $this->deleteQualificationDtl($postedData->data);
                        break;
                    case "deleteExperienceDtl":
                        $responseData = $this->deleteExperienceDtl($postedData->data);
                        break;
                    case "deleteTrainingDtl":
                        $responseData = $this->deleteTrainingDtl($postedData->data);
                        break;
                    case "pullEmployeeDetailById":
                        $responseData = $this->pullEmployeeDetailById($postedData->data);
                        break;
                    case "pullEmployeeById":
                        $responseData = $this->pullEmployeeById($postedData->data);
                        break;
                    case "pullFileTypeList":
                        $responseData = $this->pullFileTypeList();
                        break;
                    case "fetchRoleDashboards":
                        $responseData = $this->fetchRoleDashboards($postedData->data);
                        break;
                    case "assignDashboard":
                        $responseData = $this->assignDashboard($postedData->data);
                        break;
                    case "pullEmployeeList":
                        $responseData = $this->pullEmployeeList($postedData->data);
                        break;
                    case "updateDashboardAssign":
                        $responseData = $this->updateDashboardAssign($postedData->data);
                        break;
                    case "menuDelete":
                        $responseData = $this->menuDelete($postedData->data);
                        break;
                    case "pullEmployeeFile":
                        $responseData = $this->pullEmployeeFile($postedData->data);
                        break;
                    case "pushEmployeeProfile":
                        $responseData = $this->pushEmployeeProfile($postedData->data);
                        break;
                    case "pushEmployeeDocument":
                        $responseData = $this->pushEmployeeDocument($postedData->data);
                        break;
                    case "pullEmployeeFileByEmpId":
                        $responseData = $this->pullEmployeeFileByEmpId($postedData->data);
                        break;
                    case "dropEmployeeFile":
                        $responseData = $this->dropEmployeeFile($postedData->data);
                        break;
                    case "pullEmployeeListForEmployeeTable":
                        $responseData = $this->pullEmployeeListForEmployeeTable($postedData->data);
                        break;
                    case "pullJobHistoryList":
                        $responseData = $this->pullJobHistoryList($postedData->data);
                        break;
                    case 'pullLeaveRequestStatusList':
                        $responseData = $this->pullLeaveRequestStatusList($postedData->data);
                        break;
                    case "pullLoanRequestStatusList":
                        $responseData = $this->pullLoanRequestStatusList($postedData->data);
                        break;
                    case "pullTravelRequestStatusList":
                        $responseData = $this->pullTravelRequestStatusList($postedData->data);
                        break;
                    case "pullAdvanceRequestStatusList":
                        $responseData = $this->pullAdvanceRequestStatusList($postedData->data);
                        break;
                    case 'pullAttendanceRequestStatusList':
                        $responseData = $this->pullAttendanceRequestStatusList($postedData->data);
                        break;
                    case 'pullLeaveRequestList':
                        $responseData = $this->pullLeaveRequestList($postedData->data);
                        break;
                    case 'pullAttendanceRequestList':
                        $responseData = $this->pullAttendanceRequestList($postedData->data);
                        break;
                    case "checkUniqueConstraint":
                        $responseData = $this->checkUniqueConstraint($postedData->data);
                        break;
                    case "pullMonthsByFiscalYear":
                        $responseData = $this->pullMonthsByFiscalYear($postedData->data);
                        break;
                    case "deleteContent":
                        $responseData = $this->deleteContent($postedData->data);
                        break;
                    case "pullPayRollGeneratedMonths":
                        $responseData = $this->pullPayRollGeneratedMonths($postedData->data);
                        break;
                    case "fetchEmployeePaySlip":
                        $responseData = $this->fetchEmployeePaySlip($postedData->data);
                        break;
                    case "pullAttendanceList":
                        $responseData = $this->pullAttendanceList($postedData->data);
                        break;
                    case "employeeAttendanceApi":
                        $responseData = $this->employeeAttendanceApi($postedData);
                        break;
                    case "pullLoanList":
                        $responseData = $this->pullLoanList($postedData->data);
                        break;
                    case "pullAdvanceList":
                        $responseData = $this->pullAdvanceList($postedData->data);
                        break;
                    case "checkAdvanceRestriction":
                        $responseData = $this->checkAdvanceRestriction($postedData->data);
                        break;
                    case "pullAdvanceDetailByEmpId":
                        $responseData = $this->pullAdvanceDetailByEmpId($postedData->data);
                        break;
                    case "pullHolidaysForEmployee":
                        $responseData = $this->pullHolidaysForEmployee($postedData->data);
                        break;
                    case "pullDayoffWorkRequestStatusList":
                        $responseData = $this->pullDayoffWorkRequestStatusList($postedData->data);
                        break;
                    case "pullHoliayWorkRequestStatusList":
                        $responseData = $this->pullHoliayWorkRequestStatusList($postedData->data);
                        break;
                    case "pullAssetBalance":
                        $responseData = $this->pullAssetBalance($postedData->data);
                        break;
                    case "getServerDate":
                        $responseData = $this->getServerDate($postedData->data);
                        break;
                    case "pullTrainingRequestStatusList":
                        $responseData = $this->pullTrainingRequestStatusList($postedData->data);
                        break;
                    case "checkUserName":
                        $responseData = $this->checkUserName($postedData->data);
                        break;
                    case "pullOvertimeRequestStatusList":
                        $responseData = $this->pullOvertimeRequestStatusList($postedData->data);
                        break;
                    case "pullAssetIssueList":
                        $responseData = $this->pullAssetIssueList($postedData->data);
                        break;
                    case "pullServiceQuestionList":
                        $responseData = $this->pullServiceQuestionList($postedData->data);
                        break;
//                    case "pullDepartmentAccordingToBranch":
//                        $responseData = $this->pullDepartmentAccordingToBranch($postedData->data);
//                        break;
                    case "pullAttendanceWidOvertimeList":
                        $responseData = $this->pullAttendanceWidOvertimeList($postedData->data);
                        break;
                    case "pullInOutTime":
                        $responseData = $this->pullInOutTime($postedData->data);
                        break;
                    case "pullMisPunchAttendanceList";
                        $responseData = $this->pullMisPunchAttendanceList($postedData->data);
                        break;
                    case "submitAppraisalKPI":
                        $responseData = $this->submitAppraisalKPI($postedData->data);
                        break;
                    case "pullAppraisalKPIList":
                        $responseData = $this->pullAppraisalKPIList($postedData->data);
                        break;
                    case "deleteAppraisalKPI":
                        $responseData = $this->deleteAppraisalKPI($postedData->data);
                        break;
                    case "submitAppraisalCompetencies":
                        $responseData = $this->submitAppraisalCompetencies($postedData->data);
                        break;
                    case "pullAppraisalCompetenciesList":
                        $responseData = $this->pullAppraisalCompetenciesList($postedData->data);
                        break;
                    case "deleteAppraisalCompetencies":
                        $responseData = $this->deleteAppraisalCompetencies($postedData->data);
                    case "pullCurUserPwd";
                        $responseData = $this->pullCurUserPwd();
                        break;
                    case "updateCurUserPwd";
                        $responseData = $this->updateCurUserPwd($postedData->data);
                        break;
                    case "pullAppraisalViewList":
                        $responseData = $this->pullAppraisalViewList($postedData->data);
                        break;
                    default:
                        throw new Exception("action not found");
                        break;
                }
            } else {
                $responseData = [
                    "success" => false
                ];
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel(['data' => $responseData]);
    }

    private function assignEmployeeShift($data) {
        $shiftAssign = new ShiftAssign();

        $shiftAssign->employeeId = $data['employeeId'];
        $shiftAssign->shiftId = $data['shiftId'];

        $shiftAssignRepo = new ShiftAssignRepository($this->adapter);
        if (!empty($data['oldShiftId'])) {
            $shiftAssignClone = clone $shiftAssign;

            unset($shiftAssignClone->employeeId);
            unset($shiftAssignClone->shiftId);
            unset($shiftAssignClone->createdDt);

            $shiftAssignClone->status = 'D';
            $shiftAssignClone->modifiedDt = Helper::getcurrentExpressionDate();
            $shiftAssignClone->modifiedBy = $this->loggedIdEmployeeId;
            $shiftAssignRepo->edit($shiftAssignClone, [$data['employeeId'], $data['oldShiftId']]);

            $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
            $shiftAssign->createdBy = $this->loggedIdEmployeeId;
            $shiftAssign->status = 'E';
            $shiftAssignRepo->add($shiftAssign);
        } else {
            $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
            $shiftAssign->createdBy = $this->loggedIdEmployeeId;
            $shiftAssign->status = 'E';
            $shiftAssignRepo->add($shiftAssign);
        }

        return [
            "success" => true,
            "data" => $data
        ];
    }

    private function pullEmployeeMonthlyValue(array $data) {
        $monValDetRepo = new MonthlyValueDetailRepo($this->adapter);
        $empListRaw = $monValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation'], $data['company'], $data['employee']);
        $empListP = [];
        foreach ($empListRaw as $key => $emp) {
            $empListP[$key] = $emp;
        }
        $empList = [];
        $mthIds = $data['monthlyValues'];

        $mthVal = [];
        foreach ($mthIds as $mthId) {
            $tempData = $monValDetRepo->filter($data['branch'], $data['department'], $data['designation'], $mthId);
            $tempOutput = [];
            foreach ($tempData as $key => $val) {
                $val['MTH_ID'] = $mthId;
                array_push($tempOutput, $val);
            }
            array_push($mthVal, $tempOutput);
        }
//                    foreach ($empListRaw as $key => $val) {
//
//                        foreach ($mthVal as $mthValUnit) {
//                            print "a";
//                            print_r($mthValUnit);
//                            print "b";
//                            print_r($val);
//                            if (array_key_exists($key - 1, $mthValUnit)) {
//                                $val[$mthValUnit[$key - 1]['MTH_ID']] = floatval(($mthValUnit[$key - 1]['MTH_VALUE'] == null) ? 0 : $mthValUnit[$key - 1]['MTH_VALUE']);
//                            }
//                        }
//                        $empList[$key] = $val;
//                    }
        $counter = 0;
        foreach ($mthVal as $mthValUnit) {
            foreach ($empListP as $key => $val) {
                if ($counter == 0) {
                    $empList[$key] = $val;
                }
                foreach ($mthValUnit as $key1 => $val1) {
                    if ($val['EMPLOYEE_ID'] == $val1['EMPLOYEE_ID']) {
                        $empList[$key][$val1['MTH_ID']] = floatval($val1['MTH_VALUE']);

                        break;
                    } else {
                        $empList[$key][$val1['MTH_ID']] = floatval("0");
                    }
                }
            }
            $counter++;
        }
        return [
            "success" => true,
            "data" => $empList
        ];
    }

    private function pushEmployeeMonthlyValue(array $data) {
        $monValDet = new MonthlyValueDetail();
        $monValDet->employeeId = $data['employeeId'];
        $monValDet->mthId = $data['mthId'];
        $monValDet->mthValue = $data['value'];
        unset($monValDet->branchId);
        unset($monValDet->companyId);

        $monValDetRepo = new MonthlyValueDetailRepo($this->adapter);
        if ($monValDetRepo->fetchById([$data['employeeId'], $data['mthId']]) == null) {
            $monValDet->createdDt = Helper::getcurrentExpressionDate();
            $monValDet->status = 'E';
            $monValDetRepo->add($monValDet);
        } else {
            unset($monValDet->status);
            unset($monValDet->createdDt);
            unset($monValDet->employeeId);
            unset($monValDet->mthId);
            $monValDet->modifiedDt = Helper::getcurrentExpressionDate();
            $monValDetRepo->edit($monValDet, [$data['employeeId'], $data['mthId']]);
        }

        return [
            "success" => true,
            "data" => $data
        ];
    }

    private function pullEmployeeFlatValue(array $data) {
        $flatValDetRepo = new FlatValueDetailRepo($this->adapter);
        $empListRaw = $flatValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation'], $data['company'], $data['employee']);
        $empListP = [];
        foreach ($empListRaw as $key => $emp) {
            $empListP[$key] = $emp;
        }
        $empList = [];
        $mthIds = $data['flatValues'];

        $mthVal = [];
        foreach ($mthIds as $mthId) {
            $tempData = $flatValDetRepo->filter($data['branch'], $data['department'], $data['designation'], $mthId);
            $tempOutput = [];
            foreach ($tempData as $key => $val) {
                $val['MTH_ID'] = $mthId;
                array_push($tempOutput, $val);
            }
            array_push($mthVal, $tempOutput);
        }
        $counter = 0;
        foreach ($mthVal as $mthValUnit) {
            foreach ($empListP as $key => $val) {
                if ($counter == 0) {
                    $empList[$key] = $val;
                }
                foreach ($mthValUnit as $key1 => $val1) {
                    if ($val['EMPLOYEE_ID'] == $val1['EMPLOYEE_ID']) {
                        $empList[$key][$val1['FLAT_ID']] = floatval($val1['FLAT_VALUE']);
                        break;
                    } else {
                        $empList[$key][$val1['FLAT_ID']] = floatval("0");
                    }
                }
            }
            $counter++;
        }

        return [
            "success" => true,
            "data" => $empList
        ];
    }

    private function pushEmployeeFlatValue(array $data) {
        $flatValDet = new FlatValueDetail();
        $flatValDet->employeeId = $data['employeeId'];
        $flatValDet->flatId = $data['flatId'];
        $flatValDet->flatValue = $data['value'];
        unset($flatValDet->branchId);
        unset($flatValDet->companyId);

        $flatValDetRepo = new FlatValueDetailRepo($this->adapter);
        if ($flatValDetRepo->fetchById([$data['employeeId'], $data['flatId']]) == null) {
            $flatValDet->createdDt = Helper::getcurrentExpressionDate();
            $flatValDet->status = 'E';
            $flatValDetRepo->add($flatValDet);
        } else {
            unset($flatValDet->status);
            unset($flatValDet->createdDt);
            unset($flatValDet->employeeId);
            unset($flatValDet->flatId);
            $flatValDet->modifiedDt = Helper::getcurrentExpressionDate();
            $flatValDetRepo->edit($flatValDet, [$data['employeeId'], $data['flatId']]);
        }

        return [
            "success" => true,
            "data" => $data
        ];
    }

    private function pushRule(array $data = null) {
        $repository = new RulesRepository($this->adapter);
        $auth = new AuthenticationService();

        $rulesValue = new Rules();
        $rulesValue->exchangeArrayFromForm($data);
        if ($rulesValue->payId != NULL) {
            $payId = $rulesValue->payId;
            unset($rulesValue->payId);
            unset($rulesValue->createdDt);
            unset($rulesValue->createdBy);
            unset($rulesValue->status);
            unset($rulesValue->refRuleFlag);

            $rulesValue->modifiedDt = Helper::getcurrentExpressionDate();
            $rulesValue->modifiedBy = $auth->getStorage()->read()['user_id'];
            $repository->edit($rulesValue, $payId);
            return ["success" => true, "message" => "Rule successfully edited"];
        } else {
            $rulesValue->payId = ((int) Helper::getMaxId($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID)) + 1;
            $rulesValue->createdDt = Helper::getcurrentExpressionDate();
            $rulesValue->status = 'E';
            $rulesValue->refRuleFlag = 'N';

            $rulesValue->createdBy = $auth->getStorage()->read()['user_id'];
            $repository->add($rulesValue);
            return ["success" => true, "message" => "Rule successfully added", "data" => ["payId" => $rulesValue->payId]];
        }
    }

    private function pullRule(array $data = null) {
        $repository = new RulesRepository($this->adapter);
        return ["success" => true, "message" => "Rule successfully added", "data" => ["rule" => $repository->fetchById($data['ruleId'])]];
    }

    private function pushRuleDetail(array $data = null) {
        $repository = new RulesDetailRepo($this->adapter);
        $ruleDetail = new RulesDetail();

        $ruleDetail->payId = $data['payId'];
        $ruleDetail->mnenonicName = $data['mnenonicName'];
        $ruleDetail->isMonthly = ($data['isMonthly'] == 'true') ? 'Y' : 'N';
        if ($data['srNo'] == null) {
            $ruleDetail->srNo = 1;
            $repository->add($ruleDetail);
            return ["success" => true, "data" => $data];
        } else {
            $payId = $ruleDetail->payId;
            unset($ruleDetail->payId);
//            $repository->edit($ruleDetail, [RulesDetail::PAY_ID => $payId]);
            $repository->edit($ruleDetail, $payId);
            $ruleDetail->srNo = $data['srNo'];
            return ["success" => true, "data" => $data];
        }
    }

    private function pullRuleDetailByPayId(array $data = null) {
        $repository = new RulesDetailRepo($this->adapter);
        $payDetail = $repository->fetchById($data["payId"]);
        return ["success" => true, "data" => $payDetail];
    }

    private function menu($parent_menu = null) {
        $menuSetupRepository = new MenuSetupRepository($this->adapter);
        $result = $menuSetupRepository->getHierarchicalMenu($parent_menu);
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $children = $this->menu($row['MENU_ID']);
                if ($children) {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $children
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

    public function generateQuestion($headingId) {
        $questionRepo = new QuestionRepository($this->adapter);
        $result = $questionRepo->fetchByHeadingId($headingId);
        $questionList = array();
        foreach ($result as $row) {
            $questionList[] = array(
                "text" => $row['QUESTION_EDESC'],
                "id" => $row['QUESTION_ID'],
                "icon" => "fa fa-folder icon-state-success"
            );
        }
        return $questionList;
    }

    public function headingsList() {
        $headingRepo = new HeadingRepository($this->adapter);
        $result = $headingRepo->fetchAll();
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $question = $this->generateQuestion($row['HEADING_ID']);
                if ($question) {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $question
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

    private function menuInsertion($data) {
        $record = $data['dataArray'];
        $model = new MenuSetup();
        $repository = new MenuSetupRepository($this->adapter);
        $model->menuId = Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID) + 1;
        $model->menuCode = $record['menuCode'];
        $model->menuName = $record['menuName'];
        $model->route = $record['route'];
        $model->action = $record['action'];
        $model->menuIndex = $record['menuIndex'];
        $model->iconClass = $record['iconClass'];
        if ($data['parentMenu'] != null) {
            $model->parentMenu = $data['parentMenu'];
        }
        $model->menuDescription = $record['menuDescription'];
        $model->isVisible = $record['isVisible'];
        $model->status = 'E';
        $model->createdDt = Helper::getcurrentExpressionDate();
        $model->createdBy = $this->loggedIdEmployeeId;

//        $menuIndex = $repository->checkMenuIndex($record['menuIndex']);
//        if ($menuIndex) {
//            $menuIndexErr = "Menu Index Already Exist!!!";
//            $data = "";
//        } else {
        $menuIndexErr = "";
        $repository->add($model);
        $data = "Menu Successfully Added!!";
//        }
        $menuData = $this->menu();
        return $responseData = [
            "success" => true,
            "data" => $data,
            "menuData" => $menuData,
            "menuIndexErr" => $menuIndexErr
        ];
    }

    public function pullMenuDetail($data) {
        $menuId = $data['id'];
        $repository = new MenuSetupRepository($this->adapter);
        $result = $repository->fetchById($menuId);

        return $responseData = [
            "data" => $result
        ];
    }

    public function menuUpdate($data) {
        $record = $data['dataArray'];
        $model = new MenuSetup();
        $repository = new MenuSetupRepository($this->adapter);
        $menuId = $record['menuId'];
        $model->modifiedDt = Helper::getcurrentExpressionDate();
        $model->modifiedBy = $this->loggedIdEmployeeId;
        $model->menuCode = $record['menuCode'];
        $model->menuName = $record['menuName'];
        $model->route = $record['route'];
        $model->action = $record['action'];
        $model->menuIndex = $record['menuIndex'];
        $model->iconClass = $record['iconClass'];

        $model->menuDescription = $record['menuDescription'];
        $model->isVisible = $record['isVisible'];

        unset($model->status);
        unset($model->parentMenu);
        unset($model->menuId);
        unset($model->createdDt);

//        $menuIndex = $repository->checkMenuIndex($record['menuIndex'], $menuId);
//        if ($menuIndex) {
//            $menuIndexErr = "Menu Index Already Exist!!!";
//            $data = "";
//        } else {
        $menuIndexErr = "";
        $repository->edit($model, $menuId);
        $data = "Menu Successfully Updated!!";
//        }
        $menuData = $this->menu();
        return $responseData = [
            "success" => true,
            "data" => $data,
            "menuData" => $menuData,
            "menuIndexErr" => $menuIndexErr
        ];
    }

    public function permissionAssign($data) {
        $rolePermissionRepository = new RolePermissionRepository($this->adapter);
        $menuSetupRepository = new MenuSetupRepository($this->adapter);
        $rolePermissionModel = new RolePermission();

        $roleId = $data['roleId'];
        $menuId = $data['menuId'];
        $checked = $data['checked'];

//if child of same parent menu were assigned on same roleId then don't need to deactivate parent menu list
        $menuDtl = $menuSetupRepository->fetchById($menuId);
        $menuListOfSameParent = $menuSetupRepository->getMenuListOfSameParent($menuDtl['PARENT_MENU']);
        $numMenuListOfSameParent = 0;
        foreach ($menuListOfSameParent as $childOfSameParent) {
            $existChildDtl = $rolePermissionRepository->getActiveRoleMenu($childOfSameParent['MENU_ID'], $roleId);
            if ($existChildDtl) {
                $numMenuListOfSameParent += 1;
            }
        }

        $childMenuList = $menuSetupRepository->getAllCHildMenu($menuId);
        $parentMenuList = $menuSetupRepository->getAllParentMenu($menuId);

        if ($checked == "true") {
            foreach ($childMenuList as $row) {

                $result = $rolePermissionRepository->selectRoleMenu($row['MENU_ID'], $roleId);
//$num = count($result);
                if ($result) {
                    $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                } else {

                    $rolePermissionModel->roleId = $roleId;
                    $rolePermissionModel->menuId = $row['MENU_ID'];
                    $rolePermissionModel->createdDt = Helper::getcurrentExpressionDate();
                    $rolePermissionModel->status = 'E';

                    $rolePermissionRepository->add($rolePermissionModel);
                }
            }
            foreach ($parentMenuList as $row) {

                $result = $rolePermissionRepository->selectRoleMenu($row['MENU_ID'], $roleId);
//$num = count($result);
                if ($result) {
                    $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                } else {

                    $rolePermissionModel->roleId = $roleId;
                    $rolePermissionModel->menuId = $row['MENU_ID'];
                    $rolePermissionModel->createdDt = Helper::getcurrentExpressionDate();
                    $rolePermissionModel->status = 'E';

                    $rolePermissionRepository->add($rolePermissionModel);
                }
            }
            $data = "Role Successfully Assigned";
        } else if ($checked == "false") {
            foreach ($childMenuList as $row) {
                $rolePermissionRepository->deleteAll($row['MENU_ID'], $roleId);
            }
            if ($numMenuListOfSameParent == 1) {
                foreach ($parentMenuList as $row) {
                    $rolePermissionRepository->deleteAll($row['MENU_ID'], $roleId);

//need to activate those parent key whose another child key is assigned on same roleId
                    $childMenuList1 = $menuSetupRepository->getMenuListOfSameParent($row['MENU_ID']);
                    foreach ($childMenuList1 as $childRow) {
                        $getPermissionDtl = $rolePermissionRepository->getActiveRoleMenu($childRow['MENU_ID'], $roleId);
                        if ($getPermissionDtl) {
                            $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                        }
                    }
                }
            } else {
                $rolePermissionRepository->deleteAll($menuId, $roleId);
            }
            $data = "Role Assign Successfully Removed";
        }
        return $responseData = [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullRolePermissionList($data) {
        $menuId = $data['menuId'];

        $rolePermissionRepository = new RolePermissionRepository($this->adapter);
        $roleRepository = new RoleSetupRepository($this->adapter);

        $result = $roleRepository->fetchAll();
        $rolePermissionList = $rolePermissionRepository->findAllRoleByMenuId($menuId);

        $tempArray = [];
        foreach ($result as $item) {
            array_push($tempArray, $item);
        }

        $temArray1 = [];
        foreach ($rolePermissionList as $row) {
            array_push($temArray1, $row);
        }

        return $reponseData = [
            "success" => true,
            "data" => $tempArray,
            "data1" => $temArray1
        ];
    }

    public function pullServiceHistory($data) {
        $employeeId = $data['employeeId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];

        $serviceRepository = new ServiceRepository($this->adapter);
        $history = $serviceRepository->getAllHistoryWidEmpId($employeeId, $fromDate, $toDate);

        $data = [];
        foreach ($history as $row) {
            array_push($data, $row);
        }

        return $responseData = [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullPositionsAssignedByPayId($data) {
        $payId = $data["payId"];
        $payPositionRepo = new PayPositionRepo($this->adapter);
        $positions = $payPositionRepo->fetchById($payId);

        $data = [];
        foreach ($positions as $position) {
            array_push($data, $position);
        }

        return [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullLeaveBalanceDetail($data) {
        $emplyoeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $repository = new LeaveBalanceRepository($this->adapter);
        $employeeList = $repository->getAllEmployee($emplyoeeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId);

        $mainArray = [];
        foreach ($employeeList as $row) {
            $employeeId = $row['EMPLOYEE_ID'];
            if ($row['MIDDLE_NAME'] == '') {
                $employeeName = $row['FIRST_NAME'] . " " . $row['LAST_NAME'];
            } else if ($row['MIDDLE_NAME'] != '') {
                $employeeName = $row['FIRST_NAME'] . " " . $row['MIDDLE_NAME'] . " " . $row['LAST_NAME'];
            }
            $leaveList = $repository->getAllLeave();
            $childArray = [];
//loop through list of leave and if leave is not assigned then set leave balance to zero
            foreach ($leaveList as $leaveRow) {
                $leaveId = $leaveRow['LEAVE_ID'];
                $leaveBalanceDtl = $repository->getByEmpIdLeaveId($employeeId, $leaveId);
                if ($leaveBalanceDtl == false) {
                    $leaveBalance = [
                        'BALANCE' => 0,
                        'LEAVE_ID' => $leaveId,
                        'EMPLOYEE_ID' => $employeeId,
                        'SERVICE_EVENT_TYPE_ID' => 0
                    ];
                } else if ($leaveBalanceDtl != false && $leaveBalanceDtl['BALANCE'] == NULL) {
                    $leaveBalance = [
                        'BALANCE' => 0,
                        'LEAVE_ID' => $leaveId,
                        'EMPLOYEE_ID' => $employeeId,
                        'SERVICE_EVENT_TYPE_ID' => 0
                    ];
                } else {
                    $leaveBalance = $leaveBalanceDtl;
                }
                array_push($childArray, $leaveBalance);
            }
            $mainArray[$employeeName] = $childArray;
        }
        return $reponseData = [
            "success" => true,
            "allList" => $mainArray
        ];
    }

    public function pullHolidayList($data) {
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];


        $holidayRepository = new HolidayRepository($this->adapter);
        $rawList = $holidayRepository->filterRecords($fromDate, $toDate);
        $list = Helper::extractDbData($rawList);
        return $responseData = [
            "success" => true,
            "data" => $list
        ];
    }

    public function addPositionAssigned($data) {
        $payId = $data['payId'];
        $positions = $data['positions'];
        $payPositionRepo = new PayPositionRepo($this->adapter);
        $payPosition = new PayPositionSetup();
        $payPosition->payId = $payId;
        foreach ($positions as $position) {
            $payPosition->positionId = $position;
            $payPositionRepo->add($payPosition);
        }

        return ["success" => true, "data" => null];
    }

    private function deletePositionAssigned($data) {
        $payId = $data['payId'];
        $positions = $data['positions'];
        $payPositionRepo = new PayPositionRepo($this->adapter);
        foreach ($positions as $position) {
            $payPositionRepo->delete([$payId, $position]);
        }
        return ["success" => true, "data" => null];
    }

    private function fetchEmployeePaySlip($data) {
        $monthId = $data['month'];
        $auth = new AuthenticationService();
        $employeeId = $auth->getStorage()->read()['employee_id'];

        $salarySheetController = new SalarySheetController($this->adapter);
        if ($salarySheetController->checkIfGenerated($monthId)) {
            $employeeList[$employeeId] = "";
            $results = $salarySheetController->viewSalarySheet($monthId, $employeeList)[$employeeId];
        }

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchForProfileById($employeeId);
        $results['employeeDetail'] = $employee;

        $variableProcessor = new VariableProcessor($this->adapter, $employeeId, $monthId);
        $absentDays = $variableProcessor->processVariable(PayrollGenerator::VARIABLES[2]);
        $results["absentDays"] = $absentDays;

        $presentDays = $variableProcessor->processVariable(PayrollGenerator::VARIABLES[3]);
        $results["presentDays"] = $presentDays;
        return ["success" => true, "data" => $results];
    }

    public function pullAcademicDetail($data) {
        $academicDegreeRepository = new AcademicDegreeRepository($this->adapter);
        $academicUniversityRepository = new AcademicUniversityRepository($this->adapter);
        $academicProgramRepository = new AcademicProgramRepository($this->adapter);
        $academicCourseRepository = new AcademicCourseRepository($this->adapter);
        $employeeQualificationRepository = new EmployeeQualificationRepository($this->adapter);

        $degreeList = [];
        $universityList = [];
        $programList = [];
        $courseList = [];
        $employeeQualificationList = [];

        $degrees = $academicDegreeRepository->fetchAll();
        $universities = $academicUniversityRepository->fetchAll();
        $programs = $academicProgramRepository->fetchAll();
        $courses = $academicCourseRepository->fetchAll();
        $employeeQualifications = $employeeQualificationRepository->fetchByEmployeeId($data['employeeId']);

        foreach ($degrees as $row) {
            array_push($degreeList, [
                'id' => $row['ACADEMIC_DEGREE_ID'],
                'name' => $row['ACADEMIC_DEGREE_NAME']
            ]);
        }
        foreach ($universities as $row) {
            array_push($universityList, [
                'id' => $row['ACADEMIC_UNIVERSITY_ID'],
                'name' => $row['ACADEMIC_UNIVERSITY_NAME']
            ]);
        }
        foreach ($programs as $row) {
            array_push($programList, [
                'id' => $row['ACADEMIC_PROGRAM_ID'],
                'name' => $row['ACADEMIC_PROGRAM_NAME']
            ]);
        }
        foreach ($courses as $row) {
            array_push($courseList, [
                'id' => $row['ACADEMIC_COURSE_ID'],
                'name' => $row['ACADEMIC_COURSE_NAME']
            ]);
        }
        foreach ($employeeQualifications as $row) {
            $degreeRow = $academicDegreeRepository->fetchById($row['ACADEMIC_DEGREE_ID']);
            $degreeDtl = [
                'id' => $degreeRow['ACADEMIC_DEGREE_ID'],
                'name' => $degreeRow['ACADEMIC_DEGREE_NAME']
            ];
            $universityRow = $academicUniversityRepository->fetchById($row['ACADEMIC_UNIVERSITY_ID']);
            $universityDtl = [
                'id' => $universityRow['ACADEMIC_UNIVERSITY_ID'],
                'name' => $universityRow['ACADEMIC_UNIVERSITY_NAME']
            ];
            $programRow = $academicProgramRepository->fetchById($row['ACADEMIC_PROGRAM_ID']);
            $programDtl = [
                'id' => $programRow['ACADEMIC_PROGRAM_ID'],
                'name' => $programRow['ACADEMIC_PROGRAM_NAME']
            ];
            $courseRow = $academicCourseRepository->fetchById($row['ACADEMIC_COURSE_ID']);
            $courseDtl = [
                'id' => $courseRow['ACADEMIC_COURSE_ID'],
                'name' => $courseRow['ACADEMIC_COURSE_NAME']
            ];


            $documentRow = array_push($employeeQualificationList, [
                'degreeDtl' => $degreeDtl,
                'universityDtl' => $universityDtl,
                'programDtl' => $programDtl,
                'courseDtl' => $courseDtl,
                'rankType' => $row['RANK_TYPE'],
                'rankValue' => $row['RANK_VALUE'],
                'passedYr' => $row['PASSED_YR'],
                'id' => $row['ID']
            ]);
        }

        $data = [
            'degreeList' => $degreeList,
            'universityList' => $universityList,
            'programList' => $programList,
            'courseList' => $courseList,
            'num' => count($employeeQualificationList),
            'employeeQualificationList' => $employeeQualificationList
        ];

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function pullExperienceDetail($data) {
        $repository = new EmployeeExperienceRepository($this->adapter);
        $experienceList = [];
        $employeeId = (int) $data['employeeId'];
        $result = $repository->getByEmpId($employeeId);
        foreach ($result as $row) {
            array_push($experienceList, $row);
        }
        $num = count($experienceList);
        return [
            "success" => true,
            "data" => $experienceList,
            "num" => $num
        ];
    }

    public function pullTrainingDetail($data) {
        $repository = new EmployeeTrainingRepository($this->adapter);
        $trainingList = [];
        $employeeId = (int) $data['employeeId'];
        $result = $repository->getByEmpId($employeeId);
        foreach ($result as $row) {
            array_push($trainingList, $row);
        }
        $num = count($trainingList);
        return [
            'success' => true,
            'data' => $trainingList,
            'num' => $num
        ];
    }

    public function submitQualificationDtl($data) {
//$qualificationDtl = $data;
        $repository = new EmployeeQualificationRepository($this->adapter);
        $empQualificationModel = new EmployeeQualification();
        if ($data['qualificationRecordNum'] > 0) {
            foreach ($data['qualificationRecord'] as $qualificationDtl) {
                $id = $qualificationDtl['id'];
                $academicDegreeId = $qualificationDtl['academicDegreeId'];
                $academicUniversityId = $qualificationDtl['academicUniversityId'];
                $academicProgramId = $qualificationDtl['academicProgramId'];
                $academicCourseId = $qualificationDtl['academicCourseId'];
                $rankType = $qualificationDtl['rankType'];
                $rankValue = $qualificationDtl['rankValue'];
                $passedYr = $qualificationDtl['passedYr'];
                $employeeId = $data['employeeId'];

                $empQualificationModel->employeeId = $employeeId;
                $empQualificationModel->academicDegreeId = $academicDegreeId['id'];
                $empQualificationModel->academicUniversityId = $academicUniversityId['id'];
                $empQualificationModel->academicProgramId = $academicProgramId['id'];
                $empQualificationModel->academicCourseId = $academicCourseId['id'];
                $empQualificationModel->rankType = $rankType['id'];
                $empQualificationModel->rankValue = $rankValue;
                $empQualificationModel->passedYr = $passedYr;
                $empQualificationModel->createdDt = Helper::getcurrentExpressionDate();
                $empQualificationModel->status = 'E';

                if ($id != 0) {
                    $empQualificationModel->modifiedDt = Helper::getcurrentExpressionDate();
                    $repository->edit($empQualificationModel, $id);
                } else if ($id == 0) {
                    $empQualificationModel->id = Helper::getMaxId($this->adapter, EmployeeQualification::TABLE_NAME, EmployeeQualification::ID) + 1;
                    $repository->add($empQualificationModel);
                }
            }
        }
        return [
            "success" => true,
            "data" => "Qualification Detail Successfully Added"
        ];
    }

    public function submitExperienceDtl($data) {
        $experienceListEmpty = (int) $data['experienceListEmpty'];
        $employeeId = (int) $data['employeeId'];

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeExperienceRepo = new EmployeeExperienceRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById((int) $this->loggedIdEmployeeId);

        if ($experienceListEmpty == 1) {
            $experienceList = $data['experienceList'];
            foreach ($experienceList as $experience) {
                $employeeExperienceModel = new EmployeeExperience();
                $employeeExperienceModel->employeeId = (int) $employeeId;
                $employeeExperienceModel->status = 'E';
                $employeeExperienceModel->organizationType = $experience['organizationTypeId']['id'];
                $employeeExperienceModel->organizationName = $experience['organizationName'];
                $employeeExperienceModel->fromDate = $experience['fromDate'];
                $employeeExperienceModel->toDate = $experience['toDate'];
                $employeeExperienceModel->position = $experience['position'];

                $id = (int) $experience['id'];
                if ($id == 0) {
                    $employeeExperienceModel->id = (int) (Helper::getMaxId($this->adapter, EmployeeExperience::TABLE_NAME, EmployeeExperience::ID)) + 1;
                    $employeeExperienceModel->createdBy = (int) $this->loggedIdEmployeeId;
                    $employeeExperienceModel->createdDate = Helper::getcurrentExpressionDate();
                    $employeeExperienceModel->approvedDate = Helper::getcurrentExpressionDate();
                    $employeeExperienceModel->companyId = (int) $employeeDetail['COMPANY_ID'];
                    $employeeExperienceModel->branchId = (int) $employeeDetail['BRANCH_ID'];
                    $employeeExperienceRepo->add($employeeExperienceModel);
                } else {
                    $employeeExperienceModel->modifiedBy = (int) $this->loggedIdEmployeeId;
                    $employeeExperienceModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $employeeExperienceRepo->edit($employeeExperienceModel, $id);
                }
            }
        }
        return [
            "success" => true,
            "data" => "Employee Experience Detail Successfully Added"
        ];
    }

    public function submitTrainingDtl($data) {
        $trainingListEmpty = $data['trainingListEmpty'];
        $employeeId = (int) $data['employeeId'];
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeTrainingRepo = new EmployeeTrainingRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->loggedIdEmployeeId);

        if ($trainingListEmpty == 1) {
            $trainingList = $data['trainingList'];
            foreach ($trainingList as $training) {
                $employeeTrainingModel = new EmployeeTraining();
                $employeeTrainingModel->employeeId = $employeeId;
                $employeeTrainingModel->status = 'E';
                $employeeTrainingModel->trainingName = $training['trainingName'];
                $employeeTrainingModel->description = $training['description'];
                $employeeTrainingModel->fromDate = $training['fromDate'];
                $employeeTrainingModel->toDate = $training['toDate'];

                $id = (int) $training['id'];
                if ($id == 0) {
                    $employeeTrainingModel->id = ((int) Helper::getMaxId($this->adapter, EmployeeTraining::TABLE_NAME, EmployeeTraining::ID)) + 1;
                    $employeeTrainingModel->createdBy = (int) $this->loggedIdEmployeeId;
                    $employeeTrainingModel->createdDate = Helper::getcurrentExpressionDate();
                    $employeeTrainingModel->approvedDate = Helper::getcurrentExpressionDate();
                    $employeeTrainingModel->companyId = (int) $employeeDetail['COMPANY_ID'];
                    $employeeTrainingModel->branchId = (int) $employeeDetail['BRANCH_ID'];
                    $employeeTrainingRepo->add($employeeTrainingModel);
                } else {
                    $employeeTrainingModel->modifiedBy = (int) $this->loggedIdEmployeeId;
                    $employeeTrainingModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $employeeTrainingRepo->edit($employeeTrainingModel, $id);
                }
            }
        }
        return [
            "success" => true,
            "data" => "Employee Training Detail Successfully Added"
        ];
    }

    public function deleteQualificationDtl($data) {
        $id = $data['id'];
        $repository = new EmployeeQualificationRepository($this->adapter);
        $repository->delete($id);
        return[
            "success" => true,
            "data" => "Qualification Detail Successfully Removed"
        ];
    }

    public function deleteExperienceDtl($data) {
        $id = $data['id'];
        $repository = new EmployeeExperienceRepository($this->adapter);
        $repository->delete($id);
        return[
            "success" => true,
            "data" => "Experience Detail Successfully Removed"
        ];
    }

    public function deleteTrainingDtl($data) {
        $id = (int) $data['id'];
        $repository = new EmployeeTrainingRepository($this->adapter);
        $repository->delete($id);
        return[
            "success" => true,
            "data" => "Training Detail Successfully Removed"
        ];
    }

    private function pullEmployeeDetailById($data) {
        $employeeId = $data["employeeId"];
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchForProfileById($employeeId);
        return ["success" => true, "data" => $employee];
    }

    private function pullEmployeeById($data) {
        $employeeId = $data["employeeId"];
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchById($employeeId);
        return ["success" => true, "data" => $employee];
    }

    public function fetchRoleDashboards($data) {
        $roleId = $data['roleId'];
        $dashboardRepo = new DashboardDetailRepo($this->adapter);
        $result = $dashboardRepo->fetchById($roleId);
        $dashboards = [];
        foreach ($result as $dashboard) {
            array_push($dashboards, $dashboard);
        }

        return ["success" => true,
            "data" => $dashboards];
    }

    public function assignDashboard($data) {
        $dashboard = $data['dashboard'];
        $roleId = $data['roleId'];
        $status = $data['status'];
        $roleType = $data['roleType'];

        $dashboardRepo = new DashboardDetailRepo($this->adapter);

        $dashboardDetail = new DashboardDetail;
        $dashboardDetail->dashboard = $dashboard;
        $dashboardDetail->roleId = $roleId;
        $dashboardDetail->roleType = $roleType;
        if ($status == 'true') {
            $dashboardRepo->add($dashboardDetail);
        } else {
            $ids['dashboard'] = $dashboard;
            $ids['roleId'] = $roleId;
            $dashboardRepo->delete($ids);
        }

        return [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullEmployeeList($data) {
        $$employeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeList = $repository->filterRecords($$employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId);

        return [
            'success' => true,
            'data' => $employeeList
        ];
    }

    public function pullEmployeeListForEmployeeTable($data) {
        $emplyoeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $employeeTypeId = $data['employeeTypeId'];

        $repository = new EmployeeRepository($this->adapter);
        $result = $repository->filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, 1, $companyId, $employeeTypeId);
        $employeeList = [];
        foreach ($result as $row) {
            if ($row['MARITAL_STATUS'] == 'U') {
                $row['MARITAL_STATUS'] = "Unmarried";
            } else {
                $row['MARITAL_STATUS'] = "Married";
            }
            $perVdcMunicipalityDtl = $repository->getVdcMunicipalityDtl($row['ADDR_PERM_VDC_MUNICIPALITY_ID']);
            $perDistrictDtl = $repository->getDistrictDtl($perVdcMunicipalityDtl['DISTRICT_ID']);
            $perZoneDtl = $repository->getZoneDtl($perDistrictDtl['ZONE_ID']);

            $tempVdcMunicipalityDtl = $repository->getVdcMunicipalityDtl($row['ADDR_TEMP_VDC_MUNICIPALITY_ID']);
            $tempDistrictDtl = $repository->getDistrictDtl($tempVdcMunicipalityDtl['DISTRICT_ID']);
            $tempZoneDtl = $repository->getZoneDtl($tempDistrictDtl['ZONE_ID']);

            $row['ADDR_PERM_DISTRICT_NAME'] = $perDistrictDtl['DISTRICT_NAME'];
            $row['ADDR_TEMP_DISTRICT_NAME'] = $tempDistrictDtl['DISTRICT_NAME'];
            $row['ADDR_PERM_ZONE_NAME'] = $perZoneDtl['ZONE_NAME'];
            $row['ADDR_TEMP_ZONE_NAME'] = $tempZoneDtl['ZONE_NAME'];

            array_push($employeeList, $row);
        }

        return [
            'success' => true,
            'data' => $employeeList
        ];
    }

    public function pullEmployeeListForReportingRole($data) {
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeResult = $repository->filterRecords($employeeId, $branchId, $departmentId, $designationId, -1, -1, -1, 1, $companyId);

        $employeeList = [];
        $i = 0;
        foreach ($employeeResult as $employeeRow) {
            if ($employeeRow['MIDDLE_NAME'] != null) {
                $middleName = " " . $employeeRow['MIDDLE_NAME'] . " ";
            } else {
                $middleName = " ";
            }
            $employeeList [$i]["id"] = $employeeRow['EMPLOYEE_ID'];
            $employeeList [$i]["name"] = $employeeRow['FIRST_NAME'] . $middleName . $employeeRow['LAST_NAME'];
            $i++;
        }
        return [
            'success' => true,
            'data' => $employeeList
        ];
    }

    public function menuDelete($data) {
        $menuId = $data['menuId'];
        $menuRepository = new MenuSetupRepository($this->adapter);
        $rolePermissionRepository = new RolePermissionRepository($this->adapter);

        $allChildMenuList = $menuRepository->getAllCHildMenu($menuId);
        foreach ($allChildMenuList as $allChildMenu) {
            $menuDeleteResult = $menuRepository->delete($allChildMenu['MENU_ID']);
            $rolePermissionResult = $rolePermissionRepository->delete($allChildMenu['MENU_ID']);
        }
        $menuData = $this->menu();
        return [
            "success" => true,
            "menuData" => $menuData,
            "data" => "Menu with all respective detail successfully deleted!!"
        ];
    }

    public function updateDashboardAssign($data) {
        $dashboard = $data['dashboard'];
        $roleId = $data['roleId'];
        $roleType = $data['roleType'];

        $dashboardRepo = new DashboardDetailRepo($this->adapter);

        $dashboardDetail = new DashboardDetail;
        $dashboardDetail->roleType = $roleType;

        $dashboardRepo->edit($dashboardDetail, [DashboardDetail::ROLE_ID => $roleId, DashboardDetail::DASHBOARD => $dashboard]);


        return [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullEmployeeFile($data) {
        $employeeFileId = $data["employeeFileId"];

        $employeeFileRepo = new \Setup\Repository\EmployeeFile($this->adapter);
        $employeeFile = $employeeFileRepo->fetchById($employeeFileId);

        return ["success" => true, "data" => $employeeFile];
    }

    public function pullEmployeeFileByEmpId($data) {
        $employeeId = $data['employeeId'];
        $employeeFileRepo = new \Setup\Repository\EmployeeFile($this->adapter);
        $employeeFile = $employeeFileRepo->fetchByEmpId($employeeId);

        return ["success" => true, "data" => $employeeFile];
    }

    public function pushEmployeeProfile($data) {
        $employeefile = new \Setup\Model\EmployeeFile();

        if ($data['fileCode'] == null) {
            $employeefile->fileCode = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1;
//            $employeefile->employeeId = $data['employeeId'];
            $employeefile->filetypeCode = $data['fileTypeCode'];
            $employeefile->filePath = $data['filePath'];
            $employeefile->fileName = $data['fileName'];
            $employeefile->status = 'E';
            $employeefile->createdDt = Helper::getcurrentExpressionDate();

            $employeeFileRepo = new \Setup\Repository\EmployeeFile($this->adapter);
            $employeeFileRepo->add($employeefile);

            $employeeRepo = new EmployeeRepository($this->adapter);
            $employeeModel = new \Setup\Model\HrEmployees();
            $employeeModel->profilePictureId = $employeefile->fileCode;
            $employeeRepo->edit($employeeModel, $data['employeeId']);
            return["success" => true, "data" => ['fileCode' => $employeefile->fileCode]];
        } else {
            $employeefile->filetypeCode = $data['fileTypeCode'];
            $employeefile->filePath = $data['filePath'];

            $employeeFileRepo = new \Setup\Repository\EmployeeFile($this->adapter);
            $employeeFileRepo->edit($employeefile, $data['fileCode']);
            return["success" => true, "data" => ['fileCode' => $data['fileCode']]];
        }
    }

    public function dropEmployeeFile($data) {
        $employeeRepo = new \Setup\Repository\EmployeeFile($this->adapter);
        $employeeRepo->delete($data['fileCode']);
        return["success" => true, "data" => ['fileCode' => $data['fileCode']]];
    }

    public function pushEmployeeDocument($data) {
        $employeefile = new \Setup\Model\EmployeeFile();
        $employeefile->fileCode = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1;
        $employeefile->employeeId = $data['employeeId'];
        $employeefile->filetypeCode = $data['fileTypeCode'];
        $employeefile->filePath = $data['filePath'];
        $employeefile->fileName = $data['oldFileName'];
        $employeefile->status = 'E';
        $employeefile->createdDt = Helper::getcurrentExpressionDate();

        $employeeFileRepo = new \Setup\Repository\EmployeeFile($this->adapter);
        $employeeFileRepo->add($employeefile);

        return["success" => true, "data" => ['fileCode' => $employeefile->fileCode]];
    }

    public function pullJobHistoryList($data) {
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $jobHistoryRepository = new JobHistoryRepository($this->adapter);
        $result = $jobHistoryRepository->filter($fromDate, $toDate, $employeeId, $serviceEventTypeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId);

        $jobHistoryRecord = [];
        foreach ($result as $row) {
            array_push($jobHistoryRecord, $row);
        }

        return [
            "success" => "true",
            "data" => $jobHistoryRecord
        ];
    }

    public function pullLeaveRequestStatusList($data) {
        $leaveStatusRepository = new LeaveStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $leaveStatusRepository->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DT'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }
        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullLoanRequestStatusList($data) {
        $loanStatusRepository = new LoanStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $loanStatusRepository->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullTravelRequestStatusList($data) {
        $travelStatusRepository = new TravelStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $travelStatusRepository->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        $getRequestType = function($requestType) {
            if ($requestType == 'ad') {
                return "Advance";
            } else if ($requestType == 'ep') {
                return "Expense";
            } else {
                return "";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status, 'REQUESTED_TYPE' => $getRequestType($row['REQUESTED_TYPE'])]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullAdvanceRequestStatusList($data) {
        $advanceStatusRepository = new AdvanceStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $advanceStatusRepository->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullAttendanceRequestStatusList($data) {
        $attendanceStatusRepository = new AttendanceStatusRepository($this->adapter);
        if (key_exists('approverId', $data)) {
            $approverId = $data['approverId'];
        } else {
            $approverId = null;
        }
        $result = $attendanceStatusRepository->getFilteredRecord($data, $approverId);

        $recordList = [];
        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == "R") {
                return "Rejected";
            } elseif ($status == "RC") {
                return "Recommended";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        foreach ($result as $row) {

            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DT'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $approverId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $approverId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'RECOMMENDER/APPROVER';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);

            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList)
        ];
    }

    public function pullLeaveRequestList($data) {
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
        $leaveRequestList = $leaveRequestRepository->getfilterRecords($data);
        $leaveRequest = [];
        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getAction = function($status) {
            if ($status == "RQ") {
                return ["delete" => 'Cancel Request'];
            } else {
                return ["view" => 'View'];
            }
        };
        foreach ($leaveRequestList as $leaveRequestRow) {
            $status = $getValue($leaveRequestRow['STATUS']);
            $action = $getAction($leaveRequestRow['STATUS']);

            $statusId = $leaveRequestRow['STATUS'];
            $approvedDT = $leaveRequestRow['APPROVED_DT'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $leaveRequestRow['RECOMMENDER'] : $leaveRequestRow['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $leaveRequestRow['APPROVER'] : $leaveRequestRow['APPROVED_BY'];

            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $new_row = array_merge($leaveRequestRow, [
                'STATUS' => $status,
                'ACTION' => key($action),
                'ACTION_TEXT' => $action[key($action)],
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
            ]);
            $startDate = DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $leaveRequestRow['FROM_DATE']);
            $toDayDate = new DateTime();
//            if (($toDayDate < $startDate) && ($statusId == 'RQ' || $statusId == 'RC' || $statusId == 'AP')) {
//                $new_row['ALLOW_TO_EDIT'] = 1;
//            } else if (($toDayDate >= $startDate) && $statusId == 'RQ') {
//                $new_row['ALLOW_TO_EDIT'] = 1;
//            } else if ($toDayDate >= $startDate) {
//                $new_row['ALLOW_TO_EDIT'] = 0;
//            } else {
//                $new_row['ALLOW_TO_EDIT'] = 0;
//            }
            if ($statusId == 'C' || $statusId == 'R') {
                $new_row['ALLOW_TO_EDIT'] = 0;
            } else {
                $new_row['ALLOW_TO_EDIT'] = 1;
            }
            array_push($leaveRequest, $new_row);
        }
        return [
            "success" => "true",
            "data" => $leaveRequest
        ];
    }

    public function pullAttendanceRequestList($data) {
        $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);
        $attendanceList = $attendanceRequestRepository->getFilterRecords($data);
        $attendanceRequest = [];
        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        $getAction = function($status) {
            if ($status == "RQ") {
                return ["delete" => 'Cancel Request'];
            } else {
                return ["view" => 'View'];
            }
        };

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        foreach ($attendanceList as $attendanceRow) {
            $status = $getValue($attendanceRow['STATUS']);
            $action = $getAction($attendanceRow['STATUS']);

            $statusId = $attendanceRow['STATUS'];
            $approvedDT = $attendanceRow['APPROVED_DT'];

            $authApprover = ($statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $attendanceRow['APPROVER'] : $attendanceRow['APPROVED_BY'];
            $approverName = $fullName($authApprover);

            $new_row = array_merge($attendanceRow, [
                'A_STATUS' => $status,
                'ACTION' => key($action),
                'ACTION_TEXT' => $action[key($action)],
                'APPROVER_NAME' => $approverName
            ]);
            if ($statusId == 'RQ') {
                $new_row['ALLOW_TO_EDIT'] = 1;
            } else {
                $new_row['ALLOW_TO_EDIT'] = 0;
            }
            array_push($attendanceRequest, $new_row);
        }
        return [
            "success" => "true",
            "data" => $attendanceRequest
        ];
    }

    public function checkUniqueConstraint($data) {
        $tableName = $data['tableName'];
        $columnsWidValues = $data['columnsWidValues'];
        $selfId = $data['selfId'];
        if ($selfId != 'R') {
            $selfId1 = $selfId;
            $requestTbl = 0;
        } else if ($selfId == 'R') {
            $requestTbl = 1;
            $selfId1 = 0;
        }
        $checkColumnName = $data['checkColumnName'];
        $result = ConstraintHelper::checkUniqueConstraint($this->adapter, $tableName, $columnsWidValues, $checkColumnName, $selfId1, $requestTbl);
        return [
            "success" => "true",
            "data" => (int) $result,
            "msg" => "* Already Exist!!!"
        ];
    }

    public function checkUserName($data) {
        $tableName = $data['tableName'];
        $columnsWidValues = [$data['columnName'] => $data['value']];
        $result = ConstraintHelper::checkUniqueConstraint($this->adapter, $tableName, $columnsWidValues, nulll, 0, 0);
        return [
            "success" => "true",
            "data" => (int) $result,
            "msg" => "* There is no account registered for this username.!!!"
        ];
    }

    private function pullMonthsByFiscalYear($data) {
        $fiscalYearId = $data['fiscalYearId'];
        $monthRepo = new MonthRepository($this->adapter);
        $rawMonths = $monthRepo->fetchById($fiscalYearId);

        $months = Helper::extractDbData($rawMonths);
        return [
            "success" => true,
            "data" => $months
        ];
    }

    public function deleteContent($data) {
        $tableName = $data['tableName'];
        $columnName = $data['columnName'];
        $id = $data['id'];

        $result = DeleteHelper::deleteContent($this->adapter, $tableName, $columnName, $id);

        return [
            "success" => "true",
            "msg" => "Record Successfully Deleted!!!"
        ];
    }

    public function pullPayRollGeneratedMonths($data) {
        $employeeId = null;
        $joinDate = null;
        if (isset($data['employeeId'])) {
            $employeeId = $data['employeeId'];
        }
        if ($employeeId != null) {
            $result = EntityHelper::getTableKVList($this->adapter, \Setup\Model\HrEmployees::TABLE_NAME, null, [\Setup\Model\HrEmployees::JOIN_DATE], [\Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], null, null);
            if (sizeof($result) > 0) {
                $joinDate = $result[0];
            }
        }
        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $generatedSalarySheets = Helper::extractDbData($salarySheetRepo->joinWithMonth(null, $joinDate));
        return [
            "success" => "true",
            "data" => $generatedSalarySheets
        ];
    }

    private function pullEmployeeForShiftAssign(array $ids) {
        $shiftAssignRepo = new ShiftAssignRepository($this->adapter);
        $result = $shiftAssignRepo->filter($ids['branchId'], $ids['departmentId'], $ids['designationId'], $ids['positionId'], $ids['serviceTypeId'], $ids['companyId'], $ids['serviceEventTypeId'], $ids['employeeId']);

        $tempArray = [];
        foreach ($result as $item) {
            $tmp = $shiftAssignRepo->filterByEmployeeId($item['EMPLOYEE_ID']);
            if ($tmp != null) {
                $item[ShiftAssign::SHIFT_ID] = $tmp[ShiftAssign::SHIFT_ID];
                $item[ShiftSetup::SHIFT_ENAME] = $tmp[ShiftSetup::SHIFT_ENAME];
            } else {
                $item[ShiftAssign::SHIFT_ID] = "";
                $item[ShiftSetup::SHIFT_ENAME] = "";
            }
            array_push($tempArray, $item);
        }
        return [
            "success" => true,
            "data" => $tempArray
        ];
    }

    public function pullEmployeeForRecomApproverAssign($data) {
        $companyId = $data['companyId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];
        $serviceEventTypeId = (!isset($data['serviceEventTypeId']) || $data['serviceEventTypeId'] == null) ? -1 : $data['serviceEventTypeId'];
        $recommenderId = (!isset($data['recommenderId']) || $data['recommenderId'] == null) ? -1 : $data['recommenderId'];
        $approverId = (!isset($data['approverId']) || $data['approverId'] == null) ? -1 : $data['approverId'];

        $recommApproverRepo = new RecommendApproveRepository($this->adapter);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, 1, $companyId);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            $recommedApproverList = $recommApproverRepo->getDetailByEmployeeID($employeeId, $recommenderId, $approverId);
            if ($recommedApproverList != null) {
                $middleNameR = ($recommedApproverList['MIDDLE_NAME_R'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_R'] . " " : " ";
                $middleNameA = ($recommedApproverList['MIDDLE_NAME_A'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_A'] . " " : " ";

                if ($recommedApproverList['RETIRED_R'] != 'Y' && $recommedApproverList['STATUS_R'] != 'D') {
                    $employeeRow['RECOMMENDER_NAME'] = $recommedApproverList['FIRST_NAME_R'] . $middleNameR . $recommedApproverList['LAST_NAME_R'];
                    $employeeRow['RETIRED_R'] = $recommedApproverList['RETIRED_R'];
                    $employeeRow['STATUS_R'] = $recommedApproverList['STATUS_R'];
                    $employeeRow['RECOMMENDER_ID'] = $recommedApproverList['RECOMMEND_BY'];
                } else {
                    $employeeRow['RECOMMENDER_NAME'] = "";
                    $employeeRow['RETIRED_R'] = "";
                    $employeeRow['STATUS_R'] = "";
                    $employeeRow['RECOMMENDER_ID'] = null;
                }
                if ($recommedApproverList['RETIRED_A'] != 'Y' && $recommedApproverList['STATUS_A'] != 'D') {
                    $employeeRow['APPROVER_NAME'] = $recommedApproverList['FIRST_NAME_A'] . $middleNameA . $recommedApproverList['LAST_NAME_A'];
                    $employeeRow['RETIRED_A'] = $recommedApproverList['RETIRED_A'];
                    $employeeRow['STATUS_A'] = $recommedApproverList['STATUS_A'];
                    $employeeRow['APPROVER_ID'] = $recommedApproverList['APPROVED_BY'];
                } else {
                    $employeeRow['APPROVER_NAME'] = "";
                    $employeeRow['RETIRED_A'] = "";
                    $employeeRow['STATUS_A'] = "";
                    $employeeRow['APPROVER_ID'] = null;
                }
            } else {
                $employeeRow['RECOMMENDER_NAME'] = "";
                $employeeRow['RETIRED_R'] = "";
                $employeeRow['STATUS_R'] = "";
                $employeeRow['RECOMMENDER_ID'] = null;

                $employeeRow['APPROVER_NAME'] = "";
                $employeeRow['RETIRED_A'] = "";
                $employeeRow['STATUS_A'] = "";
                $employeeRow['APPROVER_ID'] = null;
            }
            array_push($employeeList, $employeeRow);
        }
        ///  print_r($employeeList); die();
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }

    public function pullTrainingAssignList($data) {
        $employeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $trainingId = $data['trainingId'];
        $companyId = $data['companyId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $result = $trainingAssignRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $trainingId, $companyId);
        $list = [];
        $getValue = function($trainingTypeId) {
            if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            } else if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            }
        };
        $sn = 1;
        foreach ($result as $row) {
            $row['TRAINING_TYPE'] = $getValue($row['TRAINING_TYPE']);
            $startDate = DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $row['START_DATE']);
            $toDayDate = new DateTime();
            if ($toDayDate < $startDate) {
                $row['ALLOW_TO_EDIT'] = 1;
            } else if ($toDayDate >= $startDate) {
                $row['ALLOW_TO_EDIT'] = 0;
            }
            $row['SN'] = $sn;
            array_push($list, $row);
            $sn += 1;
        }
        return [
            "success" => true,
            "data" => $list
        ];
    }

    public function pullEmployeeForTrainingAssign($data) {
        $employeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $trainingId = (int) $data['trainingId'];
        $companyId = $data['companyId'];
        $employeeRepository = new EmployeeRepository($this->adapter);
        $trainingAssignRepo = new TrainingAssignRepository($this->adapter);

        $employeeResult = $employeeRepository->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, -1, 1, $companyId);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            if ($trainingId != -1) {
                $trainingAssignList = $trainingAssignRepo->getDetailByEmployeeID($employeeId, $trainingId);
            } else {
                $trainingAssignList = null;
            }
            if ($trainingAssignList != null) {
                $employeeRow['TRAINING_NAME'] = $trainingAssignList['TRAINING_NAME'];
                $employeeRow['TRAINING_ID'] = $trainingAssignList['TRAINING_ID'];
                $employeeRow['START_DATE'] = $trainingAssignList['START_DATE'];
                $employeeRow['END_DATE'] = $trainingAssignList['END_DATE'];
                $employeeRow['INSTITUTE_NAME'] = $trainingAssignList['INSTITUTE_NAME'];
                $employeeRow['LOCATION'] = $trainingAssignList['LOCATION'];
            } else {
                $employeeRow['TRAINING_NAME'] = "";
                $employeeRow['TRAINING_ID'] = "";
                $employeeRow['START_DATE'] = "";
                $employeeRow['END_DATE'] = "";
                $employeeRow['INSTITUTE_NAME'] = "";
                $employeeRow['LOCATION'] = "";
            }
            array_push($employeeList, $employeeRow);
        }
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }

    public function assignEmployeeTraining($data) {
        if (!isset($data['trainingId']) || $data['trainingId'] == '' || $data['trainingId'] == -1) {
            throw new Exception('Invalid training selection.');
        }
        $trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $trainingAssignModel = new TrainingAssign();

        $trainingAssignModel->employeeId = $data['employeeId'];
        $trainingAssignModel->trainingId = $data['trainingId'];

        $emptrainingAssignedList = $trainingAssignRepo->getAllDetailByEmployeeID($data['employeeId'], $data['trainingId']);
        $empTrainingAssignedDetail = $emptrainingAssignedList->current();

        if ($empTrainingAssignedDetail != null) {
            if ($empTrainingAssignedDetail['STATUS'] == EntityHelper::STATUS_ENABLED) {
                throw new Exception('Already Assigned');
            }
            $trainingAssignClone = clone $trainingAssignModel;
            unset($trainingAssignClone->employeeId);
            unset($trainingAssignClone->trainingId);
            unset($trainingAssignClone->createdDt);

            $trainingAssignClone->status = 'E';
            $trainingAssignClone->modifiedDt = Helper::getcurrentExpressionDate();
            $trainingAssignClone->modifiedBy = $this->loggedIdEmployeeId;
            $trainingAssignRepo->edit($trainingAssignClone, [$data['employeeId'], $data['trainingId']]);
        } else {
            $trainingAssignModel->createdDt = Helper::getcurrentExpressionDate();
            $trainingAssignModel->createdBy = $this->loggedIdEmployeeId;
            $trainingAssignModel->status = 'E';
            $trainingAssignRepo->add($trainingAssignModel);
        }
        try {
            HeadNotification::pushNotification(NotificationEvents::TRAINING_ASSIGNED, $trainingAssignModel, $this->adapter, $this);
        } catch (Exception $e) {
            return[
                "success" => true,
                "data" => null,
                "message" => "Training assigned successfully with following error : " . $e->getMessage()
            ];
        }

        return [
            "success" => true,
            "data" => null,
            "message" => "Training assigned successfully."
        ];
    }

    public function cancelEmployeeTraining($data) {
        $trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $trainingAssignModel = new TrainingAssign();
        $trainingAssignModel->employeeId = $data['employeeId'];
        $trainingAssignModel->trainingId = $data['trainingId'];
        $trainingAssignModel->status = 'D';
        $trainingAssignModel->modifiedDt = Helper::getcurrentExpressionDate();
        $trainingAssignModel->modifiedBy = $this->loggedIdEmployeeId;
        $trainingAssignRepo->edit($trainingAssignModel, [$data['employeeId'], $data['trainingId']]);

//        HeadNotification::pushNotification(NotificationEvents::TRAINING_CANCELLED, $trainingAssignModel, $this->adapter, $this);
        return [
            "success" => true,
            "data" => $data
        ];
    }

    public function assignEmployeeReportingHierarchy($data) {
        $employeeId = $data['employeeId'];
        $recommenderId = $data['recommenderId'];
        $approverId = $data['approverId'];

        if ($recommenderId == "" || $recommenderId == null) {
            $recommenderIdNew = null;
        } else if ($employeeId == $recommenderId) {
            $recommenderIdNew = "";
        } else {
            $recommenderIdNew = $recommenderId;
        }

        if ($approverId == "" || $approverId == null) {
            $approverIdNew = null;
        } else if ($employeeId == $approverId) {
            $approverIdNew = "";
        } else {
            $approverIdNew = $approverId;
        }



        $recommApproverRepo = new RecommendApproveRepository($this->adapter);
        $recommendApprove = new RecommendApprove();
        $employeePreDtl = $recommApproverRepo->fetchById($employeeId);
        if ($employeePreDtl == null) {
            $recommendApprove->employeeId = $employeeId;
            $recommendApprove->recommendBy = $recommenderIdNew;
            $recommendApprove->approvedBy = $approverIdNew;
            $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
            $recommendApprove->status = 'E';
            $recommApproverRepo->add($recommendApprove);
        } else if ($employeePreDtl != null) {
            $id = $employeePreDtl['EMPLOYEE_ID'];
            $recommendApprove->employeeId = $employeeId;
            $recommendApprove->recommendBy = $recommenderIdNew;
            $recommendApprove->approvedBy = $approverIdNew;
            $recommendApprove->modifiedDt = Helper::getcurrentExpressionDate();
            $recommendApprove->status = 'E';
            $recommApproverRepo->edit($recommendApprove, $id);
        }
        return [
            "success" => true,
            "data" => $data
        ];
    }

    public function pullAttendanceList($data) {
        $attendanceDetailRepository = new AttendanceDetailRepository($this->adapter);
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $status = $data['status'];
        $missPunchOnly = ((int) $data['missPunchOnly'] == 1) ? true : false;

        $result = $attendanceDetailRepository->filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, null, false, $missPunchOnly);
        $list = Helper::extractDbData($result);
        return [
            'success' => "true",
            "data" => $list
        ];
    }

    public function employeeAttendanceApi($data) {
        if (isset($data['employeeId']) && isset($data['attendanceDt']) && isset($data['attendanceTime'])) {
            try {
                $employeeId = $data['employeeId'];
                $attendanceDt = $data['attendanceDt'];
                $attendanceTime = $data['attendanceTime'];

                $attendance = new Attendance();
                $attendance->employeeId = $employeeId;

                $attendance->attendanceDt = Helper::getExpressionDate($attendanceDt);
                $attendance->attendanceTime = Helper::getExpressionTime($attendanceTime);

                $attendanceRepo = new AttendanceDetailRepository($this->adapter);
                $check = $attendanceRepo->addAttendance($attendance);
                return ["success" => $check ? true : false];
            } catch (Exception $e) {
                return ["success" => false];
            }
        } else {
            return ["success" => false, "message" => "please supply required parameters"];
        }
    }

    public function pullLoanList($data) {
        $employeeId = $data['employeeId'];
        $loanList = LoanAdvanceHelper::getLoanList($this->adapter, $employeeId);
        return [
            "success" => true,
            "data" => $loanList
        ];
    }

    public function pullAdvanceList($data) {
        $employeeId = $data['employeeId'];
        $advanceList = LoanAdvanceHelper::getAdvanceList($this->adapter, $employeeId);
        return [
            'success' => true,
            'data' => $advanceList
        ];
    }

    public function pullHolidaysForEmployee($data) {
        $employeeId = $data['employeeId'];
        $holidayRepo = new WorkOnHolidayStatusRepository($this->adapter);
        $holidayResult = Helper::extractDbData($holidayRepo->getAttendedHolidayList($employeeId));

        return [
            'success' => true,
            'data' => Helper::extractDbData($holidayResult)
        ];
    }

    public function checkAdvanceRestriction($data) {
        $employeeId = $data['employeeId'];
        $advanceId = $data['advanceId'];
        $requestedAmount = $data['requestedAmount'];
        $terms = $data['terms'];

        $advanceRepo = new AdvanceRepository($this->adapter);

        $advanceDetail = $advanceRepo->fetchById($advanceId);
        $minSalary = $advanceDetail['MIN_SALARY_AMT'];
        $maxSalary = $advanceDetail['MAX_SALARY_AMT'];
        $amtToAllow = $advanceDetail['AMOUNT_TO_ALLOW'];
        $monthToAllow = $advanceDetail['MONTH_TO_ALLOW'];

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($employeeId);
        $salary = $employeeDetail['SALARY'];

        $allowTerms = 0;
        $allowAmt = 0;
        $errorTerms = "";
        $errorAmt = "";
        if ($terms > $monthToAllow) {
            $allowTerms = 1;
            $errorTerms = "You can request upto " . $monthToAllow . " terms!!!";
        }
        if ($terms > 1) {
            $requestedAmount = $requestedAmount / $terms;
        }
        $requestAmtPercentage = (100 / $salary) * $requestedAmount;
        $permitAmtPercentage = ($salary * $amtToAllow) / 100;
        if ($requestAmtPercentage > $amtToAllow) {
            $allowAmt = 1;
            $errorAmt = "You can request upto " . round($permitAmtPercentage, 2) . " per month.!!!";
        }
        $data = [
            'allowTerms' => $allowTerms,
            'allowAmt' => $allowAmt,
            'errorTerms' => $errorTerms,
            'errorAmt' => $errorAmt
        ];
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function pullAdvanceDetailByEmpId($data) {
        $employeeId = $data['employeeId'];
        $advanceId = $data['advanceId'];

        $advanceRepo = new AdvanceRepository($this->adapter);

        $advanceDetail = $advanceRepo->fetchById($advanceId);
        $minSalary = $advanceDetail['MIN_SALARY_AMT'];
        $maxSalary = $advanceDetail['MAX_SALARY_AMT'];
        $amtToAllow = $advanceDetail['AMOUNT_TO_ALLOW'];
        $monthToAllow = $advanceDetail['MONTH_TO_ALLOW'];

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($employeeId);
        $salary = $employeeDetail['SALARY'];
        $permitAmtPercentage = ($salary * $amtToAllow) / 100;

        if ($monthToAllow != null || $permitAmtPercentage != 0) {
            $data = [
                'allowTerms' => (int) $monthToAllow,
                'allowAmt' => $permitAmtPercentage,
            ];
        } else {
            $data = "";
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function pullDayoffWorkRequestStatusList($data) {
        $dayoffWorkStatusRepo = new WorkOnDayoffStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $dayoffWorkStatusRepo->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullHoliayWorkRequestStatusList($data) {
        $holidayWorkStatusRepo = new WorkOnHolidayStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $holidayWorkStatusRepo->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullAssetBalance($data) {
        $assetId = $data['assetId'];

        $assetIssueRepo = new IssueRepository($this->adapter);
        $assetRemQuantity = $assetIssueRepo->fetchAssetRemBalance($assetId);

        return [
            "success" => "true",
            "data" => $assetRemQuantity['QUANTITY_BALANCE']
        ];
    }

    public function getServerDate($data) {
        return ["success" => true, "data" => ["serverDate" => date(Helper::PHP_DATE_FORMAT)]];
    }

    public function pullTrainingRequestStatusList($data) {
        $trainingStatusRepo = new TrainingStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $trainingStatusRepo->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        $getValueComType = function($trainingTypeId) {
            if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            } else if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            if ($row['TRAINING_ID'] != 0) {
                $row['START_DATE'] = $row['T_START_DATE'];
                $row['END_DATE'] = $row['T_END_DATE'];
                $row['DURATION'] = $row['T_DURATION'];
                $row['TRAINING_TYPE'] = $row['T_TRAINING_TYPE'];
                $row['TITLE'] = $row['TRAINING_NAME'];
            }
            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID,
                'TRAINING_TYPE' => $getValueComType($row['TRAINING_TYPE']),
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }

            $new_row = array_merge($row, ['STATUS' => $status]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullOvertimeRequestStatusList($data) {
        $overtimeStatusRepo = new OvertimeStatusRepository($this->adapter);
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $overtimeStatusRepo->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status]);
            $overtimeDetailResult = $overtimeDetailRepo->fetchByOvertimeId($row['OVERTIME_ID']);
            $overtimeDetails = [];
            foreach ($overtimeDetailResult as $overtimeDetailRow) {
                array_push($overtimeDetails, $overtimeDetailRow);
            }
            $new_row['DETAILS'] = $overtimeDetails;
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }

    public function pullAssetIssueList($data) {
        $employeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $companyId = $data['companyId'];
        $assetId = $data['assetId'];
        $employeeRepository = new EmployeeRepository($this->adapter);

        $employeeResult = $employeeRepository->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, -1, 1, $companyId);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];

            $employeeRow['QUANTITY'] = 0;
            $employeeRow['RETURN_DATE'] = "";
            $employeeRow['PURPOSE'] = "";
            $employeeRow['REMARKS'] = "";

            array_push($employeeList, $employeeRow);
        }

        return [
            "success" => true,
            "data" => $employeeList
        ];
    }

    public function pullServiceQuestionList($data) {
        $serviceEventTypeId = $data['id'];
        $empQaId = (gettype($data['empQaId']) == 'undefined' || $data['empQaId'] == null || $data['empQaId'] == "") ? 0 : $data['empQaId'];
        $serviceQuestionRepo = new ServiceQuestionRepository($this->adapter);
        $empServiceQuestionDtlRepo = new EmpServiceQuestionDtlRepo($this->adapter);
        $result = $serviceQuestionRepo->fetchByServiceEventTypeId($serviceEventTypeId);
        $questionDtlArray = [];
        $i = 1;
        foreach ($result as $row) {
            $tempResult = $this->pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $row['QA_ID']);
            $questionAnswerDtl = $empServiceQuestionDtlRepo->fetchByEmpQaIdQaId($row['QA_ID'], $empQaId);
            if ($tempResult) {
                $questionDtlArray[] = array(
                    "sn" => $i,
                    "qaId" => $row['QA_ID'],
                    "questionEdesc" => $row['QUESTION_EDESC'],
                    "subQuestion" => true,
                    "subQuestionList" => $tempResult['array'],
                    "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                );
            } else {
                $questionDtlArray[] = array(
                    "sn" => $i,
                    "qaId" => $row['QA_ID'],
                    "questionEdesc" => $row['QUESTION_EDESC'],
                    "subQuestion" => false,
                    "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                );
            }
            $i++;
        }
        return[
            "success" => true,
            "data" => $questionDtlArray
        ];
    }

    public function pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $parentQaId = null) {
        $serviceQuestionRepo = new ServiceQuestionRepository($this->adapter);
        $empServiceQuestionDtlRepo = new EmpServiceQuestionDtlRepo($this->adapter);

        $result = $serviceQuestionRepo->fetchByServiceEventTypeId($serviceEventTypeId, $parentQaId);
        $num = count($result);
        if ($num > 0) {
            $x = 'a';
            $questionDtlArray = [];
            foreach ($result as $row) {
                $questionAnswerDtl = $empServiceQuestionDtlRepo->fetchByEmpQaIdQaId($row['QA_ID'], $empQaId);
                $tempResult = $this->pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $row['QA_ID']);
                if ($tempResult) {
                    $questionDtlArray[] = array(
                        "sn" => $x,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => true,
                        "subQuestionList" => $tempResult['array'],
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                } else {
                    $questionDtlArray[] = array(
                        "sn" => $x,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => false,
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                }
                $x++;
            }
            return ['array' => $questionDtlArray];
        } else {
            return false;
        }
    }

//    public function pullDepartmentAccordingToBranch($data){
//        $result=EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["BRANCH_ID"=>$data['branchId'],"STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, false, true);
//        return[
//            "success" => true,
//            "data" => $result
//        ];
//    }

    public function pullAttendanceWidOvertimeList($data) {
        $attendanceDetailRepository = new AttendanceDetailRepository($this->adapter);
        $overtimeRepo = new OvertimeRepository($this->adapter);
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $positionId = $data['positionId'];
        $designationId = $data['designationId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $status = $data['status'];
        $employeeTypeId = $data['employeeTypeId'];
        $overtimeOnly = (int) $data['overtimeOnly'];
        $result = $attendanceDetailRepository->filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId, true);
        $list = [];
        foreach ($result as $row) {
            if ($status == 'L') {
                $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
            } else if ($status == 'H') {
                $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
            } else if ($status == 'A') {
                $row['STATUS'] = "Absent";
            } else if ($status == 'P') {
                $row['STATUS'] = "Present";
            } else {
                if ($row['LEAVE_ENAME'] != null) {
                    $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
                } else if ($row['HOLIDAY_ENAME'] != null) {
                    $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
                } else if ($row['HOLIDAY_ENAME'] == null && $row['LEAVE_ENAME'] == null && $row['IN_TIME'] == null) {
                    $row['STATUS'] = "Absent";
                } else if ($row['IN_TIME'] != null) {
                    $row['STATUS'] = "Present";
                }
            }
            $overtimeDetailResult = $overtimeDetailRepo->fetchByOvertimeId($row['OVERTIME_ID']);
            $overtimeDetails = [];
            foreach ($overtimeDetailResult as $overtimeDetailRow) {
                array_push($overtimeDetails, $overtimeDetailRow);
            }
            $middleName = ($row['MIDDLE_NAME'] != null) ? " " . $row['MIDDLE_NAME'] . " " : " ";
            $row['EMPLOYEE_NAME'] = $row['FIRST_NAME'] . $middleName . $row['LAST_NAME'];
            $row['DETAILS'] = $overtimeDetails;
            if ($overtimeOnly == 1 && $row['OVERTIME_ID'] != null) {
                array_push($list, $row);
            } else if ($overtimeOnly == 0) {
                array_push($list, $row);
            }
        }
        return [
            'success' => "true",
            "data" => $list
        ];
    }

    public function pullInOutTime($data) {
        $attendanceDt = $data['attendanceDt'];
        $employeeId = $data['employeeId'];

        $attendanceRepository = new AttendanceRepository($this->adapter);
        $result = $attendanceRepository->fetchInOutTimeList($employeeId, $attendanceDt);
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return [
            'success' => "true",
            "data" => $list
        ];
    }

    public function pullMisPunchAttendanceList($data) {
        $attendanceDetailRepository = new AttendanceDetailRepository($this->adapter);
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $positionId = $data['positionId'];
        $designationId = $data['designationId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $status = $data['status'];
        $employeeTypeId = $data['employeeTypeId'];
        $result = $attendanceDetailRepository->filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId);
        $list = [];
        foreach ($result as $row) {
            if ($status == 'L') {
                $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
            } else if ($status == 'H') {
                $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
            } else if ($status == 'A') {
                $row['STATUS'] = "Absent";
            } else if ($status == 'P') {
                $row['STATUS'] = "Present";
            } else {
                if ($row['LEAVE_ENAME'] != null) {
                    $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
                } else if ($row['HOLIDAY_ENAME'] != null) {
                    $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
                } else if ($row['HOLIDAY_ENAME'] == null && $row['LEAVE_ENAME'] == null && $row['IN_TIME'] == null) {
                    $row['STATUS'] = "Absent";
                } else if ($row['IN_TIME'] != null) {
                    $row['STATUS'] = "Present";
                }
            }
            $middleName = ($row['MIDDLE_NAME'] != null) ? " " . $row['MIDDLE_NAME'] . " " : " ";
            $row['EMPLOYEE_NAME'] = $row['FIRST_NAME'] . $middleName . $row['LAST_NAME'];
        }
        return [
            'success' => "true",
            "data" => $list
        ];
    }

    public function submitAppraisalKPI($data) {
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        $employeeRepository = new EmployeeRepository($this->adapter);
        $KPIList = $data['KPIList'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];
        $currentUser = $data['currentUser'];
        $loggedInUser = $this->loggedIdEmployeeId;
        $loggedInUserDtl = $employeeRepository->getById($loggedInUser);
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
        $appraisalStatus = new AppraisalStatus();
        $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        try {
            foreach ($KPIList as $KPIRow) {
                $appraisalKPI = new AppraisalKPI();
                $appraisalKPI->title = $KPIRow['title'];
                $appraisalKPI->successCriteria = $KPIRow['successCriteria'];
                $appraisalKPI->weight = $KPIRow['weight'];
                $appraisalKPI->keyAchievement = $KPIRow['keyAchievement'];
                $appraisalKPI->selfRating = (is_numeric($KPIRow['selfRating'])) ? $KPIRow['selfRating'] : null;
                $appraisalKPI->appraiserRating = (is_numeric($KPIRow['appraiserRating'])) ? $KPIRow['appraiserRating'] : null;
                if ($KPIRow['sno'] == 0 || $KPIRow['sno'] == null) {
                    $appraisalKPI->sno = (int) (Helper::getMaxId($this->adapter, AppraisalKPI::TABLE_NAME, AppraisalKPI::SNO)) + 1;
                    $appraisalKPI->appraisalId = $appraisalId;
                    $appraisalKPI->employeeId = $employeeId;
                    $appraisalKPI->createdBy = $loggedInUser;
                    $appraisalKPI->createdDate = Helper::getcurrentExpressionDate();
                    $appraisalKPI->branchId = $loggedInUserDtl['BRANCH_ID'];
                    $appraisalKPI->companyId = $loggedInUserDtl['COMPANY_ID'];
                    $appraisalKPI->status = 'E';
                    $appraisalKPIRepository->add($appraisalKPI);
                } else {
                    $appraisalKPI->modifiedBy = $loggedInUser;
                    $appraisalKPI->modifiedDate = Helper::getcurrentExpressionDate();
                    if ($appraisalKPI->employeeId != $loggedInUser) {
                        $appraisalKPI->approvedBy = $loggedInUser;
                        $appraisalKPI->approvedDate = Helper::getcurrentExpressionDate();
                    }
                    $appraisalKPIRepository->edit($appraisalKPI, $KPIRow['sno']);
                }
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 7) {
                $appraisalAssignRepo->updateCurrentStageByAppId(AppraisalHelper::getNextStageId($this->adapter, $assignedAppraisalDetail['STAGE_ORDER_NO'] + 1), $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 5) {
                $annualRatingKPI = $data['annualRatingKPI'];
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::ANNUAL_RATING_KPI => $annualRatingKPI], $appraisalId, $employeeId);
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $annualRatingKPI], $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 7) {
                switch ($currentUser) {
                    case 'appraisee':
                        HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        $adminList = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList as $adminRow) {
                            HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $adminRow['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        $appEmp = [
            'appraisalId' => $appraisalId,
            'employeeId' => $employeeId
        ];
        return [
            'success' => true,
        ];
    }

    public function pullAppraisalKPIList($data) {
        $appraisalId = $data['appraisalId'];
        $employeeId = $data['employeeId'];
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        $result = $appraisalKPIRepository->fetchByAppEmpId($employeeId, $appraisalId);
        $list = [];
        try {
            foreach ($result as $row) {
                array_push($list, $row);
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => $list
        ];
    }

    public function deleteAppraisalKPI($data) {
        $sno = $data['sno'];
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        try {
            $appraisalKPIRepository->delete($sno);
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => [
                'msg' => 'Appraisal KPI deleted successfully!!!'
            ]
        ];
    }

    public function submitAppraisalCompetencies($data) {
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        $employeeRepository = new EmployeeRepository($this->adapter);
        $competenciesList = $data['competenciesList'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];
        $currentUser = $data['currentUser'];
        $loggedInUser = $this->loggedIdEmployeeId;
        $loggedInUserDtl = $employeeRepository->getById($loggedInUser);
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
        $appraisalStatus = new AppraisalStatus();
        $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        try {
            foreach ($competenciesList as $competenciesRow) {
                $appraisalCompetencies = new AppraisalCompetencies();
                $appraisalCompetencies->title = $competenciesRow['title'];
                $appraisalCompetencies->rating = $competenciesRow['rating'];
                $appraisalCompetencies->comments = $competenciesRow['comments'];
                if ($competenciesRow['sno'] == 0 || $competenciesRow['sno'] == null) {
                    $appraisalCompetencies->sno = (int) (Helper::getMaxId($this->adapter, AppraisalCompetencies::TABLE_NAME, AppraisalCompetencies::SNO)) + 1;
                    $appraisalCompetencies->appraisalId = $appraisalId;
                    $appraisalCompetencies->employeeId = $employeeId;
                    $appraisalCompetencies->createdBy = $loggedInUser;
                    $appraisalCompetencies->createdDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetencies->branchId = $loggedInUserDtl['BRANCH_ID'];
                    $appraisalCompetencies->companyId = $loggedInUserDtl['COMPANY_ID'];
                    $appraisalCompetencies->approvedDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetencies->status = 'E';
                    $appraisalCompetenciesRepo->add($appraisalCompetencies);
                } else if ($competenciesRow['sno'] != 0) {
                    $appraisalCompetencies->modifiedBy = $loggedInUser;
                    $appraisalCompetencies->modifiedDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetenciesRepo->edit($appraisalCompetencies, $competenciesRow['sno']);
                }
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 5) {
                $annualRatingCompetency = $data['annualRatingCompetency'];
                $appraiserOverallRating = $data['appraiserOverallRating'];
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::ANNUAL_RATING_COMPETENCY => $annualRatingCompetency], $appraisalId, $employeeId);
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $appraiserOverallRating], $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 1) {
                switch ($currentUser) {
                    case 'appraisee':
                        HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        if ($assignedAppraisalDetail['ALT_APPRAISER_ID'] != null && $assignedAppraisalDetail['ALT_APPRAISER_ID'] != "") {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['ALT_APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        }
                        if ($assignedAppraisalDetail['ALT_REVIEWER_ID'] != null && $assignedAppraisalDetail['ALT_REVIEWER_ID'] != "") {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['ALT_REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        }
                        $adminList = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList as $adminRow) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $adminRow['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                    case 'appraiser':
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $employeeId, 'USER_TYPE' => "APPRAISEE"]);
                        $adminList1 = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList1 as $adminRow1) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $adminRow1['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                    case 'reviewer':
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $employeeId, 'USER_TYPE' => "APPRAISEE"]);
                        $adminList1 = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList1 as $adminRow1) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $adminRow1['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        $appEmp = [
            'appraisalId' => $appraisalId,
            'employeeId' => $employeeId
        ];
        return [
            'success' => true
        ];
    }

    public function pullAppraisalCompetenciesList($data) {
        $appraisalId = $data['appraisalId'];
        $employeeId = $data['employeeId'];
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        $result = $appraisalCompetenciesRepo->fetchByAppEmpId($employeeId, $appraisalId);
        $list = [];
        try {
            foreach ($result as $row) {
                array_push($list, $row);
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => $list
        ];
    }

    public function deleteAppraisalCompetencies($data) {
        $sno = $data['sno'];
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        try {
            $appraisalCompetenciesRepo->delete($sno);
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => [
                'msg' => 'Appraisal Competencies deleted successfully!!!'
            ]
        ];
    }

    public function pullCurUserPwd() {
        $userrepo = new UserSetupRepository($this->adapter);
        $userLoginData = $userrepo->getUserByEmployeeId($this->loggedIdEmployeeId);
        $oldPassword = $userLoginData['PASSWORD'];
        return [
            'success' => "true",
            "data" => $oldPassword
        ];
    }

    public function updateCurUserPwd($postData) {
        $newPassword = $postData['newPassword'];
        $userrepo = new UserSetupRepository($this->adapter);
        $updateResult = $userrepo->updateByEmpId($this->loggedIdEmployeeId, $newPassword);
        return [
            'success' => "true",
//            "data" => $updateResult
        ];
    }

    public function pullAppraisalViewList($data) {
        $appraisalStatusRepo = new AppraisalReportRepository($this->adapter);

        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $appraisalId = $data['appraisalId'];
        $appraisalStageId = $data['appraisalStageId'];
        $userId = $data['userId'];
        $reportType = $data['reportType'];

        $result = $appraisalStatusRepo->fetchFilterdData($fromDate, $toDate, $employeeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $appraisalId, $appraisalStageId, $reportType, $userId);
        $list = [];
        $fullName = function($id) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($this->adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $getValue = function($val) {
            if ($val != null) {
                if ($val == 'Y')
                    return 'Yes';
                else if ($val == 'N')
                    return 'No';
            }else {
                return "";
            }
        };
        foreach ($result as $row) {
            $row['APPRAISER_NAME'] = $fullName($row['APPRAISER_ID']);
            $row['ALT_APPRAISER_NAME'] = $fullName($row['ALT_APPRAISER_ID']);
            $row['REVIEWER_NAME'] = $fullName($row['REVIEWER_ID']);
            $row['ALT_REVIEWER_NAME'] = $fullName($row['ALT_REVIEWER_ID']);
            $row['APPRAISEE_AGREE'] = $getValue($row['APPRAISEE_AGREE']);
            array_push($list, $row);
        }
        return [
            "success" => true,
            'data' => $list
        ];
    }

}
