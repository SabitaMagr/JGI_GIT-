<?php

namespace RestfulService\Controller;

use Application\Helper\ConstraintHelper;
use Application\Helper\DeleteHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Months;
use Application\Repository\MonthRepository;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use AttendanceManagement\Repository\ShiftAssignRepository;
use HolidayManagement\Repository\HolidayRepository;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveStatusRepository;
use Payroll\Controller\PayrollGenerator;
use Payroll\Controller\SalarySheet as SalarySheetController;
use Payroll\Controller\VariableProcessor;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Model\PayPositionSetup;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Model\SalarySheet;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\PayPositionRepo;
use Payroll\Repository\RulesDetailRepo;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use SelfService\Repository\AttendanceRequestRepository;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Repository\ServiceRepository;
use Setup\Model\EmployeeQualification;
use Setup\Model\RecommendApprove;
use Setup\Repository\AcademicCourseRepository;
use Setup\Repository\AcademicDegreeRepository;
use Setup\Repository\AcademicProgramRepository;
use Setup\Repository\AcademicUniversityRepository;
use Setup\Repository\BranchRepository;
use Setup\Repository\EmployeeQualificationRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\JobHistoryRepository;
use Setup\Repository\RecommendApproveRepository;
use System\Model\DashboardDetail;
use System\Model\MenuSetup;
use System\Model\RolePermission;
use System\Repository\DashboardDetailRepo;
use System\Repository\MenuSetupRepository;
use System\Repository\RolePermissionRepository;
use System\Repository\RoleSetupRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RestfulService extends AbstractRestfulController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
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
        if (sizeof($files) > 0) {
            $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
            $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
            $unique = Helper::generateUniqueName();
            $newFileName = $unique . "." . $ext;
            $success = move_uploaded_file($files['file']['tmp_name'], \Setup\Controller\EmployeeController::UPLOAD_DIR . "/" . $newFileName);
            if ($success) {
                $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
            }
        } else if ($request->isPost()) {
            $postedData = $request->getPost();
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

                case "menuUpdate":
                    $responseData = $this->menuUpdate($postedData->data);
                    break;
                case "pullEmployeeListForReportingRole":
                    $responseData = $this->pullEmployeeListForReportingRole($postedData->data);
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
                case "generataMonthlySheet":
                    $responseData = $this->generataMonthlySheet($postedData->data);
                    break;
                case "pullAcademicDetail":
                    $responseData = $this->pullAcademicDetail($postedData->data);
                    break;
                case "submitQualificationDtl":
                    $responseData = $this->submitQualificationDtl($postedData->data);
                    break;
                case "deleteQualificationDtl":
                    $responseData = $this->deleteQualificationDtl($postedData->data);
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
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
        } else {
            $responseData = [
                "success" => false
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
            $shiftAssignRepo->edit($shiftAssignClone, [$data['employeeId'], $data['oldShiftId']]);

            $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
            $shiftAssign->status = 'E';
            $shiftAssignRepo->add($shiftAssign);
        } else {
            $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
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
        $empListRaw = $monValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation']);
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
        $empListRaw = $flatValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation']);
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
        $model->status = 'E';
        $model->createdDt = Helper::getcurrentExpressionDate();

        $menuIndex = $repository->checkMenuIndex($record['menuIndex']);
        if ($menuIndex) {
            $menuIndexErr = "Menu Index Already Exist!!!";
            $data = "";
        } else {
            $menuIndexErr = "";
            $repository->add($model);
            $data = "Menu Successfully Added!!";
        }
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
        $model->menuCode = $record['menuCode'];
        $model->menuName = $record['menuName'];
        $model->route = $record['route'];
        $model->action = $record['action'];
        $model->menuIndex = $record['menuIndex'];
        $model->iconClass = $record['iconClass'];

        $model->menuDescription = $record['menuDescription'];

        unset($model->status);
        unset($model->parentMenu);
        unset($model->menuId);
        unset($model->createdDt);

        $menuIndex = $repository->checkMenuIndex($record['menuIndex'], $menuId);
        if ($menuIndex) {
            $menuIndexErr = "Menu Index Already Exist!!!";
            $data = "";
        } else {
            $menuIndexErr = "";
            $repository->edit($model, $menuId);
            $data = "Menu Successfully Updated!!";
        }
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
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $repository = new LeaveBalanceRepository($this->adapter);
        $employeeList = $repository->getAllEmployee($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId);

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
                        'SERVICE_EVENT_TYPE_ID'=>0
                    ];
                } else if ($leaveBalanceDtl != false && $leaveBalanceDtl['BALANCE'] == NULL) {
                    $leaveBalance = [
                        'BALANCE' => 0,
                        'LEAVE_ID' => $leaveId,
                        'EMPLOYEE_ID' => $employeeId,
                        'SERVICE_EVENT_TYPE_ID'=>0
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
        $branchId = $data['branchId'];
        $genderId = $data['genderId'];

        if ($genderId == -1) {
            $genderId = null;
        } else {
            $genderId = $genderId;
        }

        $holidayRepository = new HolidayRepository($this->adapter);
        $list = $holidayRepository->filterRecords($fromDate, $toDate, $branchId, $genderId);

        $data = [];
        foreach ($list as $row) {
            if ($row['GENDER_NAME'] != null) {
                $row['GENDER_NAME'] = $row['GENDER_NAME'];
            } else {
                $row['GENDER_NAME'] = 'All';
            }

            if ($row['HALFDAY'] == 'F') {
                $row['HALFDAY'] = 'First Half';
            } else if ($row['HALFDAY'] == 'S') {
                $row['HALFDAY'] = 'Second Half';
            } else if ($row['HALFDAY'] == 'N') {
                $row['HALFDAY'] = 'Full Day';
            }

            if ($branchId != -1) {
                $branchRepository = new BranchRepository($this->adapter);
                $branchDtl = $branchRepository->fetchById($branchId);
                $childData = [];
                array_push($childData, $branchDtl);
                $row['BRANCHES'] = $childData;
                array_push($data, $row);
            } else if ($branchId == -1) {
                $holidayBranch = $holidayRepository->selectHolidayBranch($row['HOLIDAY_ID']);
                $childData = [];
                foreach ($holidayBranch as $childRow) {
                    array_push($childData, $childRow);
                }
                $row['BRANCHES'] = $childData;
                array_push($data, $row);
            }
        }
        return $responseData = [
            "success" => true,
            "data" => $data
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

    private function generataMonthlySheet($data) {
        $employeeId = $data['employee'];
        $monthId = $data['month'];
        $branchId = $data['branch'];
        $regenerateFlag = ($data['regenerateFlag'] == "true") ? 1 : 0;

        $monthRepo = new MonthRepository($this->adapter);
        $monthDetail = $monthRepo->fetchByMonthId($monthId);

        $results = [];
        $salarySheetController = new SalarySheetController($this->adapter);

        if ($salarySheetController->checkIfGenerated($monthId) && !$regenerateFlag) {
            $employeeList = null;
            if ($branchId == -1) {
                if ($employeeId == -1) {
                    $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                } else {
                    $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), \Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                }
            } else {
                if ($employeeId == -1) {
                    $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::BRANCH_ID => $branchId, \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                } else {
                    $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::BRANCH_ID => $branchId, \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), \Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                }
            }
            $results = $salarySheetController->viewSalarySheet($monthId, $employeeList);
        } else {
            if ($regenerateFlag) {
                $salarySheetController->deleteSalarySheetDetail($monthId);
                $salarySheetController->deleteSalarySheet($monthId);
            }
            $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
//            print "<pre>";
            foreach ($employeeList as $key => $employee) {
//                print $key;
                $generateMonthlySheet = new PayrollGenerator($this->adapter, $monthId);
                $result = $generateMonthlySheet->generate($key);
                $results[$key] = $result;
            }
//            exit;
            $addSalarySheetRes = $salarySheetController->addSalarySheet($monthId);
            if ($addSalarySheetRes != null) {
                $salarySheetController->addSalarySheetDetail($monthId, $results, $addSalarySheetRes[SalarySheet::SHEET_NO]);

                $employeeList = null;
                if ($branchId == -1) {
                    if ($employeeId == -1) {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                    } else {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), \Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                    }
                } else {
                    if ($employeeId == -1) {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::BRANCH_ID => $branchId, \Setup\Model\HrEmployees::JOIN_DATE . " >= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                    } else {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', \Setup\Model\HrEmployees::BRANCH_ID => $branchId, \Setup\Model\HrEmployees::JOIN_DATE . " >= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), \Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                    }
                }
                $results = $salarySheetController->viewSalarySheet($monthId, $employeeList);
            } else {
                $results = null;
//            handle failure here
            }
        }


//        if ($branchId == -1) {
//            if ($employeeId == -1) {
//                $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E'], ' ');
//                foreach ($employeeList as $key => $employee) {
//                    $generateMonthlySheet = new PayrollGenerator($this->adapter);
//                    $result = $generateMonthlySheet->generate($key);
//                    $results[$key] = $result;
//                }
//            } else {
//                $generateMonthlySheet = new PayrollGenerator($this->adapter);
//                $result = $generateMonthlySheet->generate($employeeId);
//                $results[$employeeId] = $result;
//            }
//        } else {
//            if ($employeeId == -1) {
//                $employeeList = EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId], ' ');
//                foreach ($employeeList as $key => $employee) {
//                    $generateMonthlySheet = new PayrollGenerator($this->adapter);
//                    $result = $generateMonthlySheet->generate($key);
//                    $results[$key] = $result;
//                }
//            } else {
//                $generateMonthlySheet = new PayrollGenerator($this->adapter);
//                $result = $generateMonthlySheet->generate($employeeId);
//                $results[$employeeId] = $result;
//            }
//        }
//        exit;
        return ["success" => true, "data" => $results];
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
            'employeeQualificationList' => $employeeQualificationList
        ];

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function submitQualificationDtl($data) {
//$qualificationDtl = $data;
        $repository = new EmployeeQualificationRepository($this->adapter);
        $empQualificationModel = new EmployeeQualification();

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
//        $empDtlId = [
//          'employeeId'=>$data['employeeId']
//        ];
//        return $data = $this->pullAcademicDetail($empDtlId);

        return [
            "success" => true,
            "data" => "Qualification Detail Successfully Added"
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
        $emplyoeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeList = $repository->filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId);

        return [
            'success' => true,
            'data' => $employeeList
        ];
    }

    public function pullEmployeeListForEmployeeTable($data) {
        $emplyoeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $repository = new EmployeeRepository($this->adapter);
        $result = $repository->filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, 1);

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
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeResult = $repository->filterRecords(-1, $branchId, $departmentId, $designationId, -1, -1, -1, 1);

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
            $employeefile->fileCode = ((int) Helper::getMaxId($this->adapter, 'HR_EMPLOYEE_FILE', 'FILE_CODE')) + 1;
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
        $employeefile->fileCode = ((int) Helper::getMaxId($this->adapter, 'HR_EMPLOYEE_FILE', 'FILE_CODE')) + 1;
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
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $jobHistoryRepository = new JobHistoryRepository($this->adapter);
        $result = $jobHistoryRepository->filter($fromDate, $toDate, $employeeId, $serviceEventTypeId);

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
            $status = $getValue($row['STATUS']);
            $role = $getRole($row['RECOMMENDER'], $row['APPROVER'], $recomApproveId);
            if ($role == 3 && $row['STATUS'] == 'RC') {
                $status = "Pending";
            }
            $role = [
                'YOUR_ROLE' => $getRoleDtl($row['RECOMMENDER'], $row['APPROVER'], $recomApproveId),
                'ROLE' => $role
            ];
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
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        foreach ($result as $row) {
            $status = $getValue($row['STATUS']);
            $new_row = array_merge($row, ['STATUS' => $status, 'YOUR_ROLE' => 'Approver']);
            array_push($recordList, $new_row);
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
            $new_row = array_merge($leaveRequestRow, ['STATUS' => $status, 'ACTION' => key($action), 'ACTION_TEXT' => $action[key($action)]]);
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
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        foreach ($attendanceList as $attendanceRow) {
            $status = $getValue($attendanceRow['STATUS']);
            $new_row = array_merge($attendanceRow, ['A_STATUS' => $status]);
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
        $checkColumnName = $data['checkColumnName'];
        $result = ConstraintHelper::checkUniqueConstraint($this->adapter, $tableName, $columnsWidValues, $checkColumnName, $selfId);
        return [
            "success" => "true",
            "data" => $result,
            "msg" => "* Already Exist!!!"
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
        $result = $shiftAssignRepo->filter($ids['branchId'], $ids['departmentId'], $ids['designationId'], $ids['positionId'], $ids['serviceTypeId']);

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
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];

        $recommApproverRepo = new RecommendApproveRepository($this->adapter);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, -1, -1, -1, 1);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            $recommedApproverList = $recommApproverRepo->getDetailByEmployeeID($employeeId);
            if ($recommedApproverList != null) {
                if ($recommedApproverList['MIDDLE_NAME_R'] != null) {
                    $middleNameR = " " . $recommedApproverList['MIDDLE_NAME_R'] . " ";
                } else {
                    $middleNameR = " ";
                }
                if ($recommedApproverList['MIDDLE_NAME_A'] != null) {
                    $middleNameA = " " . $recommedApproverList['MIDDLE_NAME_A'] . " ";
                } else {
                    $middleNameA = " ";
                }
                $employeeRow['RECOMMENDER_NAME'] = $recommedApproverList['FIRST_NAME_R'] . $middleNameR . $recommedApproverList['LAST_NAME_R'];
                $employeeRow['APPROVER_NAME'] = $recommedApproverList['FIRST_NAME_A'] . $middleNameR . $recommedApproverList['LAST_NAME_A'];
            } else {
                $employeeRow['RECOMMENDER_NAME'] = "";
                $employeeRow['APPROVER_NAME'] = "";
            }
            array_push($employeeList, $employeeRow);
        }
        ///  print_r($employeeList); die();
        return [
            "success" => true,
            "data" => $employeeList
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

}
