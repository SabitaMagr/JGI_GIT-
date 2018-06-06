<?php
namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use Exception;
use Payroll\Model\SalarySheet;
use Payroll\Model\SalarySheetDetail;
use Payroll\Model\TaxSheet;
use Payroll\Repository\PayrollRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Payroll\Repository\SalarySheetRepo;
use Payroll\Repository\SSPayValueModifiedRepo;
use Payroll\Repository\TaxSheetRepo;
use Payroll\Service\PayrollGenerator;
use Payroll\Service\SalarySheetService;
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
        $data['salarySheetList'] = iterator_to_array($this->salarySheetRepo->fetchAll(), false);
        $links['viewLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'viewSalarySheet']);
        $links['generateLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'generateSalarySheet']);
        $links['getSalarySheetListLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getSalarySheetList']);
        $links['getSearchDataLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getSearchData']);
        $links['getGroupListLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getGroupList']);
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
        $sheetNoList = $data['sheetNo'];
        $salarySheetController = new SalarySheetService($this->adapter);
        $salarySheetList = [];
        foreach ($sheetNoList as $sheetNo) {
            $salarySheet = $salarySheetController->viewSalarySheet($sheetNo);
            $salarySheetList = array_merge($salarySheetList, $salarySheet);
        }

        return new JsonModel(['success' => true, 'data' => $salarySheetList, 'error' => '']);
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
                    $companyIdList = $data['companyId'];
                    $groupIdList = $data['groupId'];
                    /*  */
                    /*  */
                    $returnData = [];
                    foreach ($companyIdList as $companyId) {
                        foreach ($groupIdList as $groupId) {
                            $sheetNo = $salarySheet->newSalarySheet($monthId, $year, $monthNo, $fromDate, $toDate, $companyId, $groupId);
                            $this->salarySheetRepo->generateSalShReport($sheetNo);
                            $salarySheetDetailRepo->delete($sheetNo);
                            $taxSheetRepo->delete($sheetNo);
                            $employeeList = $salarySheet->fetchEmployeeList($companyId, $groupId);
                            $data = null;
                            $data['sheetNo'] = $sheetNo;
                            $data['employeeList'] = $employeeList;
                            array_push($returnData, $data);
                        }
                    }
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

    public function getGroupListAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = EntityHelper::getTableList($this->adapter, "HRIS_SALARY_SHEET_GROUP", ["GROUP_ID", "GROUP_NAME"]);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

    public function payValueModifiedAction() {
        $data['getSearchDataLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getSearchData']);
        $data['getGroupListLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getGroupList']);
        $data['getFiscalYearMonthLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'getFiscalYearMonth']);
        $data['pvmReadLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'pvmRead']);
        $data['pvmUpdateLink'] = $this->url()->fromRoute('salarySheet', ['action' => 'pvmUpdate']);

        $rulesRepo = new RulesRepository($this->adapter);
        $data['ruleList'] = $rulesRepo->fetchSSRules();
        return ['data' => json_encode($data)];
    }

    public function pvmReadAction() {
        $request = $this->getRequest();
        $postData = $request->getPost();
        $sspvmRepo = new SSPayValueModifiedRepo($this->adapter);
        $data = $sspvmRepo->filter($postData['monthId'], $postData['companyId'], $postData['groupId']);

        return new JsonModel($data);
    }

    public function pvmUpdateAction() {
        $request = $this->getRequest();
        $postData = $request->getPost();

        $monthId = $postData->monthId;
        $data = json_decode($postData->models);

        $dataToUpdate = [];
        foreach ($data as $value) {
            $item = (array) $value;
            $common = ['EMPLOYEE_ID' => $item['EMPLOYEE_ID'], 'MONTH_ID' => $monthId];
            foreach ($item as $k => $v) {
                if (!in_array($k, ['EMPLOYEE_ID', 'FULL_NAME', 'COMPANY_ID', 'COMPANY_NAME', 'GROUP_ID', 'GROUP_NAME', 'MONTH_ID'])) {
                    if ($v != null) {
                        $payId = str_replace('H_', '', $k);
                        $dataUnit = array_merge($common, []);
                        $dataUnit['PAY_ID'] = $payId;
                        $dataUnit['VAL'] = $v;
                        array_push($dataToUpdate, $dataUnit);
                    }
                }
            }
        }
        $sspvmRepo = new SSPayValueModifiedRepo($this->adapter);
        $sspvmRepo->bulkEdit($dataToUpdate);
        return new JsonModel($data);
    }
}
