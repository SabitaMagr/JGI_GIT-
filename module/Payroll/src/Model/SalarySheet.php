<?php

namespace Payroll\Model;

use Application\Model\Model;

class SalarySheet extends Model {

    const TABLE_NAME = "HRIS_SALARY_SHEET";
    const SHEET_NO = "SHEET_NO";
    const MONTH_ID = "MONTH_ID";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $sheetNo;
    public $monthId;
    public $status;
    public $remarks;
    public $createdDt;
    public $modifiedDt;
    public $mappings = [
        'sheetNo' => self::SHEET_NO,
        'monthId' => self::MONTH_ID,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
    ];

}
