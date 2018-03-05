<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Controller\SalarySheet as SalarySheetController;
use Payroll\Model\SalarySheet;
use Payroll\Model\SalarySheetDetail;
use Payroll\Repository\PayrollRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Payroll\Repository\SalarySheetRepo;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class Generate extends HrisController {

    private $salarySheetRepo;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(PayrollRepository::class);
        $this->salarySheetRepo = new SalarySheetRepo($adapter);
    }

    public function indexAction() {
        $ruleRepo = new RulesRepository($this->adapter);
        $data['ruleList'] = Helper::extractDbData($ruleRepo->fetchAll());
        $data['fiscalYearList'] = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        $monthRepo = new MonthRepository($this->adapter);
        $data['monthList'] = Helper::extractDbData($monthRepo->fetchAll());
        $data['salarySheetList'] = Helper::extractDbData($this->salarySheetRepo->fetchAll());
        $links['viewLink'] = $this->url()->fromRoute('generate', ['action' => 'viewSalarySheet']);
        $links['generateLink'] = $this->url()->fromRoute('generate', ['action' => 'generateSalarySheet']);
        $links['getSalarySheetListLink'] = $this->url()->fromRoute('generate', ['action' => 'getSalarySheetList']);
        $data['links'] = $links;
        return $this->stickFlashMessagesTo(['data' => json_encode($data)]);
    }

    public function getSalarySheetListAction() {
        try {
            $list = Helper::extractDbData($this->salarySheetRepo->fetchAll());
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]]);
        }
    }

    public function viewSalarySheetAction() {
        $request = $this->getRequest();
        $data = $request->getPost();
        $sheetId = $data['sheetNo'];
        $salarySheetController = new SalarySheetController($this->adapter);
        $salarySheet = $salarySheetController->viewSalarySheet($sheetId);

        return new JsonModel(['success' => true, 'data' => $salarySheet, 'error' => '']);
    }

    public function generateSalarySheetAction() {
        $salarySheetRepo = new SalarySheetController($this->adapter);
        $salarySheetDetailRepo = new SalarySheetDetailRepo($this->adapter);
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
                    $sheetNo = $salarySheetRepo->newSalarySheet($monthId, $year, $monthNo, $fromDate, $toDate);
                    /*  */
                    $employeeList = $salarySheetRepo->fetchEmployeeList($fromDate, $toDate);
                    $returnData['sheetNo'] = $sheetNo;
                    $returnData['employeeList'] = $employeeList;
                    break;
                case 2:
                    $employeeId = $data['employeeId'];
                    $monthId = $data['monthId'];
                    $sheetNo = $data['sheetNo'];
                    $payrollGenerator = new PayrollGenerator($this->adapter);
                    $returnData = $payrollGenerator->generate($employeeId, $monthId);

                    $salarySheetDetail = new SalarySheetDetail();
                    $salarySheetDetail->sheetNo = $sheetNo;
                    $salarySheetDetail->employeeId = $employeeId;

                    foreach ($returnData['ruleValueKV'] as $key => $value) {
                        $salarySheetDetail->payId = $key;
                        $salarySheetDetail->val = $value;
                        $salarySheetDetailRepo->add($salarySheetDetail);
                    }
                    break;
                case 3:break;
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
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]]);
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

}
