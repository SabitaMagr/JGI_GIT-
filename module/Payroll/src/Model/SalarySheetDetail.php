<?php

namespace Payroll\Model;

use Application\Model\Model;

class SalarySheetDetail extends Model {

    const TABLE_NAME = "HRIS_SALARY_SHEET_DETAIL";
    const SHEET_NO = "SHEET_NO";
    const MONTH_ID = "MONTH_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const PAY_ID = "PAY_ID";
    const VAL = "VAL";
    const TOTAL_VAL = "TOTAL_VAL";

    public $sheetNo;
    public $monthId;
    public $employeeId;
    public $payId;
    public $val;
    public $totalVal;
    public $mappings = [
        'sheetNo' => self::SHEET_NO,
        'monthId' => self::MONTH_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'payId' => self::PAY_ID,
        'val' => self::VAL,
        'totalVal' => self::TOTAL_VAL,
    ];

}
