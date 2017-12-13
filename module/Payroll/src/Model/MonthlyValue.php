<?php

namespace Payroll\Model;

use Application\Model\Model;

class MonthlyValue extends Model {

    const TABLE_NAME = "HRIS_MONTHLY_VALUE_SETUP";
    const MTH_ID = "MTH_ID";
    const MTH_CODE = "MTH_CODE";
    CONST MTH_EDESC = "MTH_EDESC";
    CONST MTH_LDESC = "MTH_LDESC";
    CONST STATUS = "STATUS";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";

    public $mthId;
    public $mthCode;
    public $mthEdesc;
    public $mthLdesc;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $mappings = [
        'mthId' => self::MTH_ID,
        'mthCode' => self::MTH_CODE,
        'mthEdesc' => self::MTH_EDESC,
        'mthLdesc' => self::MTH_LDESC,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
    ];

}
