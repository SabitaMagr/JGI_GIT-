<?php

namespace Payroll\Model;

use Application\Model\Model;

class Rules extends Model {

    const TABLE_NAME = "HRIS_PAY_SETUP";
    const PAY_ID = "PAY_ID";
    const PAY_CODE = "PAY_CODE";
    const PAY_EDESC = "PAY_EDESC";
    const PAY_LDESC = "PAY_LDESC";
    const PAY_TYPE_FLAG = "PAY_TYPE_FLAG";
    const PRIORITY_INDEX = "PRIORITY_INDEX";
    const REF_RULE_FLAG = "REF_RULE_FLAG";
    const REF_PAY_ID = "REF_PAY_ID";
    const REMARKS = "REMARKS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const STATUS = "STATUS";
    const IS_MONTHLY = "IS_MONTHLY";
    const FORMULA = "FORMULA";

    public $payId;
    public $payCode;
    public $payEdesc;
    public $payLdesc;
    public $payTypeFlag;
    public $priorityIndex;
    public $refPayId;
    public $refRuleFlag;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDt;
    public $modifiedDt;
    public $modifiedBy;
    public $status;
    public $isMonthly;
    public $formula;
    public $mappings = [
        'payId' => self::PAY_ID,
        'payCode' => self::PAY_CODE,
        'payEdesc' => self::PAY_EDESC,
        'payLdesc' => self::PAY_LDESC,
        'payTypeFlag' => self::PAY_TYPE_FLAG,
        'priorityIndex' => self::PRIORITY_INDEX,
        'refPayId' => self::REF_PAY_ID,
        'refRuleFlag' => self::REF_RULE_FLAG,
        'remarks' => self::REMARKS,
        'companyId' => self::COMPANY_ID,
        'branchId' => self::BRANCH_ID,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'status' => self::STATUS,
        'isMonthly' => self::IS_MONTHLY,
        'formula' => self::FORMULA,
    ];

}
