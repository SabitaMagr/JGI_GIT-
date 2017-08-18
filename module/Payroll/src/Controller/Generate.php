<?php

namespace Payroll\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Controller\SalarySheet as SalarySheetController;
use Payroll\Model\Rules;
use Payroll\Model\SalarySheet;
use Payroll\Repository\PayrollRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\EmployeeRepository;

class Generate extends AbstractActionController {

    private $adapter;
    private $payrollRepo;
    private $employeeId;
	private $employeeCode;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->payrollRepo = new PayrollRepository($this->adapter);
        $auth = new AuthenticationService();
		$employeeRepo = new EmployeeRepository($this->adapter);
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
		$this->employeeCode = $employeeRepo->fetchById($this->employeeId)['EMPLOYEE_CODE'];
    }

    public function indexAction() {
        $rules = EntityHelper::getTableKVListWithSortOption($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID, [Rules::PAY_EDESC], [Rules::STATUS => 'E'], Rules::PRIORITY_INDEX, Select::ORDER_ASCENDING, null, false, true);
        $fiscalYears = $this->payrollRepo->fetchFiscalYears();

        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
                    'fiscalYears' => $fiscalYears,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function payslipAction(){
        return Helper::addFlashMessagesToArray($this, ['employeeId'=>$this->employeeId,'employeeCode'=>$this->employeeCode]);
    }
    public function printPayslipAction(){
        $employeeid = $this->params()->fromRoute('id');
        $mcode = $this->params()->fromRoute('mcode');
        return Helper::addFlashMessagesToArray($this, ['employeeId'=>$employeeid,'mcode'=>$mcode]);
    }
    public function taxsheetAction(){
        return Helper::addFlashMessagesToArray($this, ['employeeId'=>$this->employeeId,'employeeCode'=>$this->employeeCode]);
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
            $payrollRepo = new PayrollRepository($this->adapter);
            $employeeList = $payrollRepo->fetchEmployeeList();

            if ($regenerateFlag) {
                $salarySheetController->deleteSalarySheetDetail($monthId);
                $salarySheetController->deleteSalarySheet($monthId);
            }
            if (!($salarySheetController->checkIfGenerated($monthId))) {
                $salarySheetDetails = [];
                foreach ($employeeList as $employee) {
                    $generateMonthlySheet = new PayrollGenerator($this->adapter, $monthId);
                    $result = $generateMonthlySheet->generate($employee['EMPLOYEE_ID']);
                    $salarySheetDetails[$employee['EMPLOYEE_ID']] = $result;
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
