<?php

namespace Payroll\Controller;

use Application\Helper\Helper;
use Payroll\Model\Rules;
use Payroll\Model\SalarySheet as SalarySheetModel;
use Payroll\Model\SalarySheetDetail as SalarySheetDetailModel;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Payroll\Repository\SalarySheetRepo;

class SalarySheet {

    private $adapter;
    private $salarySheetRepo;
    private $salarySheetDetailRepo;

    public function __construct($adapter) {
        $this->adapter = $adapter;
        $this->salarySheetRepo = new SalarySheetRepo($this->adapter);
        $this->salarySheetDetailRepo = new SalarySheetDetailRepo($this->adapter);
    }

    public function addSalarySheet(int $monthId) {
        $salarySheetModal = new SalarySheetModel();
        $salarySheetModal->sheetNo = ((int) Helper::getMaxId($this->adapter, SalarySheetModel::TABLE_NAME, SalarySheetModel::SHEET_NO)) + 1;
        $salarySheetModal->monthId = $monthId;
        $salarySheetModal->createdDt = Helper::getcurrentExpressionDate();
        $salarySheetModal->status = 'E';

        if ($this->salarySheetRepo->add($salarySheetModal)) {
            return [SalarySheetModel::SHEET_NO => $salarySheetModal->sheetNo];
        } else {
            return null;
        }
    }

    public function addSalarySheetDetail(int $monthId, array $salarySheetDetails, int $salarySheet) {
        foreach ($salarySheetDetails as $empId => $salarySheetDetail) {
            $salarySheetDetailModel = new SalarySheetDetailModel($this->adapter);
            $salarySheetDetailModel->employeeId = $empId;
            $salarySheetDetailModel->monthId = $monthId;
            $salarySheetDetailModel->sheetNo = $salarySheet;

            $payRepo = new RulesRepository($this->adapter);
            $payListRaw = $payRepo->fetchAll();

            $payList = Helper::extractDbData($payListRaw);

            foreach ($payList as $pay) {
                $payId = $pay[Rules::PAY_ID];
                $salarySheetDetailModel->payId = $payId;
                $salarySheetDetailModel->val = isset($salarySheetDetail['ruleValueKV'][$payId]) ? $salarySheetDetail['ruleValueKV'][$payId] : 0;
                $salarySheetDetailModel->totalVal = $salarySheetDetail['calculatedValue'];
                $this->salarySheetDetailRepo->add($salarySheetDetailModel);
            }
        }
    }

    public function viewSalarySheet(int $monthId, array $employeeList) {
        $results = [];
        foreach ($employeeList as $empId => $empDetail) {
            $filter = [];
            $filter[SalarySheetDetailModel::EMPLOYEE_ID] = $empId;
            $filter[SalarySheetDetailModel::MONTH_ID] = $monthId;
            $test = $this->salarySheetDetailRepo->fetchById($filter);

            print "<pre>";
            print_r(Helper::extractDbData($test));
            exit;
        }
    }

    public function checkIfGenerated(int $monthId) {
        $salarySheets = $this->salarySheetRepo->fetchByIds([SalarySheetModel::MONTH_ID => $monthId]);
        if ($salarySheets->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
