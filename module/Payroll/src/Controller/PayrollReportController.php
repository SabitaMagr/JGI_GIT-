<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Exception;
use Payroll\Repository\PayrollReportRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class PayrollReportController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(PayrollReportRepo::class);
//        $this->initializeForm(Variance::class);
    }

    public function indexAction() {
        echo 'NO Action';
        die();
    }

    public function varianceAction() {
        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        $months = EntityHelper::getTableList($this->adapter, Months::TABLE_NAME, [Months::MONTH_ID, Months::MONTH_EDESC, Months::FISCAL_YEAR_ID]);

        $columnsList = $this->repository->getVarianceColumns();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'fiscalYears' => $fiscalYears,
                    'months' => $months,
                    'columnsList' => $columnsList,
        ]);
    }

    public function pullVarianceListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $results = $this->repository->getVarianceReprot($data);


            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function gradeBasicAction() {
        $datas['otVariables'] = $this->repository->getGbVariables();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'datas' => $datas
        ]);
    }

    public function pullGradeBasicAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $defaultColumnsList = $this->repository->getOtDefaultColumns();
            $reportType = $data['reportType'];
            if ($reportType == 'S') {
                $results = $this->repository->getGradeBasicSummary($data);
            } else {
                $results = $this->repository->getGradeBasicReport($data);
            }
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function basicMonthlyReportAction() {
        $otVariables = $this->repository->getGbVariables();

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'otVariables' => $otVariables
        ]);
    }

    public function basicMonthlyAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $defaultColumnsList = $this->repository->getOtMonthlyDefaultColumns($data['fiscalId']);
            $results = $this->repository->getBasicMonthly($data, $defaultColumnsList);
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    // menu for this action not inserted
    public function groupSheetAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('S', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('S');

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
//                    'fiscalYears' => $fiscalYears,
//                    'months' => $months,
                    'nonDefaultList' => $nonDefaultList,
                    'groupVariables' => $groupVariables
        ]);
    }

    public function pullGroupSheetAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $reportType = $data['reportType'];
            $groupVariable = $data['groupVariable'];

            if ($reportType == "GS") {
                $defaultColumnsList = $this->repository->getDefaultColumns('S');
                $resultData = $this->repository->getGroupReport('S', $data);
            } elseif ($reportType == "GD") {
                $defaultColumnsList = $this->repository->getVarianceDetailColumns($groupVariable);
                $resultData = $this->repository->getGroupDetailReport($data);
            }
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function groupTaxReportAction() {
        $nonDefaultList = $this->repository->getSalaryGroupColumns('T', 'N');
        $groupVariables = $this->repository->getSalaryGroupColumns('T');

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
//                    'fiscalYears' => $fiscalYears,
//                    'months' => $months,
                    'nonDefaultList' => $nonDefaultList,
                    'groupVariables' => $groupVariables
        ]);
    }

    public function pullGroupTaxReportAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $reportType = $data['reportType'];
            $groupVariable = $data['groupVariable'];

            if ($reportType == "GS") {
                $defaultColumnsList = $this->repository->getDefaultColumns('T');
                $resultData = $this->repository->getGroupReport('T', $data);
            } elseif ($reportType == "GD") {
                $defaultColumnsList = $this->repository->getVarianceDetailColumns($groupVariable);
                $resultData = $this->repository->getGroupDetailReport($data);
            }
            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($resultData);
            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function monthlySummaryAction() {


        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function pullMonthlySummaryAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $resultData = [];
            $resultData['additionDetail'] = $this->repository->fetchMonthlySummary('A', $data);
            $resultData['deductionDetail'] = $this->repository->fetchMonthlySummary('D', $data);

            $result = [];
            $result['success'] = true;
            $result['data'] = $resultData;
//            $result['columns'] = $defaultColumnsList;
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
