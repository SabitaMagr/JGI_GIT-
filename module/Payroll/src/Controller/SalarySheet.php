<?php

namespace Payroll\Controller;

use Application\Helper\Helper;
use Payroll\Model\SalarySheet as SalarySheetModel;
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

    public function addSalarySheet(int $monthId, array $salarySheetDetail) {
        $salarySheetModal = new SalarySheetModel();
        $salarySheetModal->sheetNo = ((int) Helper::getMaxId($this->adapter, SalarySheetModel::TABLE_NAME, SalarySheetModel::SHEET_NO)) + 1;
        $salarySheetModal->monthId = $monthId;
        $salarySheetModal->createdDt = Helper::getcurrentExpressionDate();
        $salarySheetModal->status = 'E';

        print "<pre>";
        print $this->salarySheetRepo->add($salarySheetModal);
        exit;
        return [SalarySheetModel::SHEET_NO => $salarySheetModal->sheetNo];
    }

    public function addSalarySheetDetail() {
        
    }

    public function viewSalarySheet() {
        
    }

}
