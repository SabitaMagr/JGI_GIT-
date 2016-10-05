<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/2/16
 * Time: 4:47 PM
 */

namespace Payroll\Model;


use Application\Model\Model;

class MonthlyValue extends Model
{
    const TABLE_NAME="HR_MONTHLY_VALUE_SETUP";

    const MTH_ID = "MTH_ID";
    const MTH_CODE = "MTH_CODE";
    CONST MTH_EDESC = "MTH_EDESC";
    CONST MTH_LDESC = "MTH_LDESC";
    CONST SHOW_AT_RULE = "SHOW_AT_RULE";
    CONST SH_INDEX_NO = "SH_INDEX_NO";
    CONST COMPANY_ID = "COMPANY_ID";
    CONST BRANCH_ID = "BRANCH_ID";
    CONST STATUS = "STATUS";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_DT = "MODIFIED_DT";

    public $mthId;
    public $mthCode;
    public $mthEdesc;
    public $mthLdesc;
    public $showAtRule;
    public $shIndexNo;
    public $companyId;
    public $branchId;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'mthId' => self::MTH_ID,
        'mthCode'=>self::MTH_CODE,
        'mthEdesc'=>self::MTH_EDESC,
        'mthLdesc'=>self::MTH_LDESC,
        'showAtRule'=>self::SHOW_AT_RULE,
        'shIndexNo'=>self::SH_INDEX_NO,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];

}