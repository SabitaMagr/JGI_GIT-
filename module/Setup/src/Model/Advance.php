<?php
namespace Setup\Model;

use Application\Model\Model;

class Advance extends Model{
    const TABLE_NAME = "HRIS_ADVANCE_MASTER_SETUP";
    const ADVANCE_ID = "ADVANCE_ID";
    const ADVANCE_CODE = "ADVANCE_CODE";
    const ADVANCE_NAME = "ADVANCE_NAME";
    const MIN_SALARY_AMT = "MIN_SALARY_AMT";
    const MAX_SALARY_AMT = "MAX_SALARY_AMT";
    const AMOUNT_TO_ALLOW = "AMOUNT_TO_ALLOW";
    const MONTH_TO_ALLOW = "MONTH_TO_ALLOW";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $advanceId;
    public $advanceCode;
    public $advanceName;
    public $minSalaryAmt;
    public $maxSalaryAmt;
    public $monthToAllow;
    public $amountToAllow;
    public $remarks;
    public $status;
    public $createdDate;
    public $createdBy;
    public $modifiedDate;
    public $modifiedBy;
    
    public $mappings = [
        'advanceId'=>self::ADVANCE_ID,
        'advanceCode'=>self::ADVANCE_CODE,
        'advanceName'=>self::ADVANCE_NAME,
        'minSalaryAmt'=>self::MIN_SALARY_AMT,
        'maxSalaryAmt'=>self::MAX_SALARY_AMT,
        'monthToAllow'=>self::MONTH_TO_ALLOW,
        'amountToAllow'=>self::AMOUNT_TO_ALLOW,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdDate'=>self::CREATED_DATE,
        'createdBy'=>self::CREATED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'modifiedBy'=>self::MODIFIED_BY
    ];
}