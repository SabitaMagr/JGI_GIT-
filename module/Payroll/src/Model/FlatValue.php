<?php

namespace Payroll\Model;

use Application\Model\Model;

class FlatValue extends Model {

    CONST TABLE_NAME = "HRIS_FLAT_VALUE_SETUP";
    CONST FLAT_ID = "FLAT_ID";
    CONST FLAT_CODE = "FLAT_CODE";
    CONST FLAT_EDESC = "FLAT_EDESC";
    CONST FLAT_LDESC = "FLAT_LDESC";
    CONST SHOW_AT_RULE = "SHOW_AT_RULE";
    CONST FLAT_FORMULA = "FLAT_FORMULA";
    CONST COMPANY_ID = "COMPANY_ID";
    CONST BRANCH_ID = "BRANCH_ID";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST STATUS = "STATUS";
    CONST REMARKS = "REMARKS";

    public $flatId;
    public $flatCode;
    public $flatEdesc;
    public $flatLdesc;
    public $showAtRule;
    public $flatFormula;
    public $companyId;
    public $branchId;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $mappings = [
        'flatId' => self::FLAT_ID,
        'flatCode' => self::FLAT_CODE,
        'flatEdesc' => self::FLAT_EDESC,
        'flatLdesc' => self::FLAT_LDESC,
        'showAtRule' => self::SHOW_AT_RULE,
        'flatFormula' => self::FLAT_FORMULA,
        'companyId' => self::COMPANY_ID,
        'branchId' => self::BRANCH_ID,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
    ];

}
