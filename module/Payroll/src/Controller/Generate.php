<?php

namespace Payroll\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Controller\SalarySheet as SalarySheetController;
use Payroll\Model\Rules;
use Payroll\Model\SalarySheet;
use Setup\Model\Branch;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;

class Generate extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E'], "FIRST_NAME", "ASC", ' ', false, true);
        $rules = EntityHelper::getTableKVListWithSortOption($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID, [Rules::PAY_EDESC], [Rules::STATUS => 'E'], Rules::PRIORITY_INDEX, Select::ORDER_ASCENDING, null, false, true);
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], null, null, null, false, true);
        $fiscalYears = EntityHelper::getTableKVListWithSortOption($this->adapter, FiscalYear::TABLE_NAME, FiscalYear::FISCAL_YEAR_ID, [FiscalYear::START_DATE, FiscalYear::END_DATE], [FiscalYear::STATUS => 'E'], null, null, "-", false, true);

        return Helper::addFlashMessagesToArray($this, [
                    'employeeList' => $employeeList,
                    'rules' => $rules,
                    'branches' => $branches,
                    'fiscalYears' => $fiscalYears
        ]);
    }

    public function generateMonthlySheetAction() {
        try {
            $request = $this->getRequest();
            if (!($request->isPost())) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();

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
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                    } else {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                    }
                } else {
                    if ($employeeId == -1) {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId, HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                    } else {
                        $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId, HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                    }
                }
                $results = $salarySheetController->viewSalarySheet($monthId, $employeeList);
            } else {
                if ($regenerateFlag) {
                    $salarySheetController->deleteSalarySheetDetail($monthId);
                    $salarySheetController->deleteSalarySheet($monthId);
                }
                $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                foreach ($employeeList as $key => $employee) {
                    $generateMonthlySheet = new PayrollGenerator($this->adapter, $monthId);
                    $result = $generateMonthlySheet->generate($key);
                    $results[$key] = $result;
                }
                $addSalarySheetRes = $salarySheetController->addSalarySheet($monthId);
                if ($addSalarySheetRes != null) {
                    $salarySheetController->addSalarySheetDetail($monthId, $results, $addSalarySheetRes[SalarySheet::SHEET_NO]);

                    $employeeList = null;
                    if ($branchId == -1) {
                        if ($employeeId == -1) {
                            $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                        } else {
                            $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
                        }
                    } else {
                        if ($employeeId == -1) {
                            $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId, HrEmployees::JOIN_DATE . " >= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');
                        } else {
                            $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId, HrEmployees::JOIN_DATE . " >= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression(), HrEmployees::EMPLOYEE_ID => $employeeId], ' ');
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
//                $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E'], ' ');
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
//                $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::BRANCH_ID => $branchId], ' ');
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
            return new CustomViewModel(['success' => true, 'data' => $results, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
