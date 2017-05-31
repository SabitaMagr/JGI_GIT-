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
        $rules = EntityHelper::getTableKVListWithSortOption($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID, [Rules::PAY_EDESC], [Rules::STATUS => 'E'], Rules::PRIORITY_INDEX, Select::ORDER_ASCENDING, null, false, true);
        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::START_DATE, FiscalYear::END_DATE]);

        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
                    'fiscalYears' => $fiscalYears,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function generateMonthlySheetAction() {
        try {
            $request = $this->getRequest();
            if (!($request->isPost())) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();

            $monthId = $data['month'];
            $regenerateFlag = ($data['regenerateFlag'] == "true") ? 1 : 0;

            $monthRepo = new MonthRepository($this->adapter);
            $monthDetail = $monthRepo->fetchByMonthId($monthId);

            $salarySheetController = new SalarySheetController($this->adapter);
            $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', HrEmployees::JOIN_DATE . " <= " . Helper::getExpressionDate($monthDetail[Months::TO_DATE])->getExpression()], ' ');

            if (!($salarySheetController->checkIfGenerated($monthId))) {
                if ($regenerateFlag) {
                    $salarySheetController->deleteSalarySheetDetail($monthId);
                    $salarySheetController->deleteSalarySheet($monthId);
                }
                $salarySheetDetails = [];
                $generateMonthlySheet = new PayrollGenerator($this->adapter, $monthId);
                foreach ($employeeList as $key => $employee) {
                    $result = $generateMonthlySheet->generate($key);
                    $salarySheetDetails[$key] = $result;
                }
                $addSalarySheetRes = $salarySheetController->addSalarySheet($monthId);
                if ($addSalarySheetRes == null) {
                    throw new Exception('Salary Sheet is null');
                }
                $salarySheetController->addSalarySheetDetail($monthId, $salarySheetDetails, $addSalarySheetRes[SalarySheet::SHEET_NO]);
            }
            $results = $salarySheetController->viewSalarySheet($monthId, $employeeList);

            return new CustomViewModel(['success' => true, 'data' => $results, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
