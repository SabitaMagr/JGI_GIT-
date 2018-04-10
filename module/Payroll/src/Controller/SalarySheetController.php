<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Controller\SalarySheet as SalarySheetService;
use Payroll\Model\SalarySheet;
use Payroll\Model\SalarySheetDetail;
use Payroll\Model\TaxSheet;
use Payroll\Repository\PayrollRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Payroll\Repository\SalarySheetRepo;
use Payroll\Repository\TaxSheetRepo;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class SalarySheetController extends HrisController {

    private $salarySheetRepo;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(PayrollRepository::class);
        $this->salarySheetRepo = new SalarySheetRepo($adapter);
    }

    public function indexAction() {
        $ruleRepo = new RulesRepository($this->adapter);
        $data['ruleList'] = iterator_to_array($ruleRepo->fetchAll(), false);
        $data['fiscalYearList'] = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        $monthRepo = new MonthRepository($this->adapter);
        $data['monthList'] = iterator_to_array($monthRepo->fetchAll(), false);
        $data['salarySheetList'] = iterator_to_array($this->salarySheetRepo->fetchAll(), false);
        $links['viewLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'viewSalarySheet']);
        $links['generateLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'generateSalarySheet']);
        $links['getSalarySheetListLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getSalarySheetList']);
        $data['links'] = $links;
        return $this->stickFlashMessagesTo(['data' => json_encode($data)]);
    }

    public function getSalarySheetListAction() {
        try {
            $list = iterator_to_array($this->salarySheetRepo->fetchAll(), false);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]]);
        }
    }

    public function viewSalarySheetAction() {
        $request = $this->getRequest();
        $data = $request->getPost();
        $sheetId = $data['sheetNo'];
        $salarySheetController = new SalarySheetService($this->adapter);
        $salarySheet = $salarySheetController->viewSalarySheet($sheetId);

        return new JsonModel(['success' => true, 'data' => $salarySheet, 'error' => '']);
    }

    public function generateSalarySheetAction() {
        $salarySheet = new SalarySheetService($this->adapter);
        $salarySheetDetailRepo = new SalarySheetDetailRepo($this->adapter);
        $taxSheetRepo = new TaxSheetRepo($this->adapter);
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $stage = $data['stage'];

            $returnData = null;
            switch ($stage) {
                case 1:
                    $monthId = $data['monthId'];
                    $year = $data['year'];
                    $monthNo = $data['monthNo'];
                    $fromDate = $data['fromDate'];
                    $toDate = $data['toDate'];
                    /*  */
                    $sheetNo = $salarySheet->newSalarySheet($monthId, $year, $monthNo, $fromDate, $toDate);
                    $this->salarySheetRepo->generateSalShReport($sheetNo);
                    /*  */
//                    $employeeList = $salarySheet->fetchEmployeeList($fromDate, $toDate);
                    $employeeList = [
                        ['EMPLOYEE_ID' => 292],
                        ['EMPLOYEE_ID' => 147],
                        ['EMPLOYEE_ID' => 212],
                    ];
                    $returnData['sheetNo'] = $sheetNo;
                    $returnData['employeeList'] = $employeeList;
                    break;
                case 2:
                    $employeeId = $data['employeeId'];
                    $monthId = $data['monthId'];
                    $sheetNo = $data['sheetNo'];
                    $payrollGenerator = new PayrollGenerator($this->adapter);
                    $returnData = $payrollGenerator->generate($employeeId, $monthId, $sheetNo);

                    $salarySheetDetail = new SalarySheetDetail();
                    $salarySheetDetail->sheetNo = $sheetNo;
                    $salarySheetDetail->employeeId = $employeeId;

                    foreach ($returnData['ruleValueKV'] as $key => $value) {
                        $salarySheetDetail->payId = $key;
                        $salarySheetDetail->val = $value;
                        $salarySheetDetailRepo->add($salarySheetDetail);
                    }

                    $taxSheet = new TaxSheet();
                    $taxSheet->sheetNo = $sheetNo;
                    $taxSheet->employeeId = $employeeId;
                    foreach ($returnData['ruleTaxValueKV'] as $key => $value) {
                        $taxSheet->payId = $key;
                        $taxSheet->val = $value;
                        $taxSheetRepo->add($taxSheet);
                    }
                    break;
                case 3:
                    break;
            }

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage(), 'stackTrace' => $e->getTrace()]);
        }
    }

    public function generateMonthlySheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $monthId = $data['month'];
            $regenerateFlag = ($data['regenerateFlag'] == "true") ? 1 : 0;

            $monthRepo = new MonthRepository($this->adapter);
            $monthDetail = $monthRepo->fetchByMonthId($monthId);

            $salarySheetController = new SalarySheetService($this->adapter);
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

            return new JsonModel(['success' => true, 'data' => $results, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]]);
        }
    }

    public function pullPayRollGeneratedMonthsAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $employeeId = null;
            $joinDate = null;
            if (isset($data['employeeId'])) {
                $employeeId = $data['employeeId'];
            }
            if ($employeeId != null) {
                $result = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, null, [HrEmployees::JOIN_DATE], [HrEmployees::EMPLOYEE_ID => $employeeId], null, null);
                if (sizeof($result) > 0) {
                    $joinDate = $result[0];
                }
            }
            $salarySheetRepo = new SalarySheetRepo($this->adapter);
            $generatedSalarySheets = Helper::extractDbData($salarySheetRepo->joinWithMonth(null, $joinDate));

            return new JsonModel(['success' => true, 'data' => $generatedSalarySheets, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function payslipAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $salarySheetDetailRepo = new SalarySheetDetailRepo($this->adapter);
                $data = $salarySheetDetailRepo->fetchEmployeePaySlip($postedData['monthId'], $postedData['employeeId']);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
