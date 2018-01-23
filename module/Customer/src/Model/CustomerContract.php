<?php

namespace Customer\Model;

use Application\Model\Model;

class CustomerContract extends Model {

    CONST TABLE_NAME = "HRIS_CUSTOMER_CONTRACT";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST START_DATE = "START_DATE";
    CONST END_DATE = "END_DATE";
    CONST IN_TIME = "IN_TIME";
    CONST OUT_TIME = "OUT_TIME";
    CONST WORKING_HOURS = "WORKING_HOURS";
    CONST WORKING_CYCLE = "WORKING_CYCLE";
    CONST CHARGE_TYPE = "CHARGE_TYPE";
    CONST CHARGE_RATE = "CHARGE_RATE";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";
    CONST CONTRACT_NAME = "CONTRACT_NAME";

    public $contractId;
    public $customerId;
    public $startDate;
    public $endDate;
    public $inTime;
    public $outTime;
    public $workingHours;
    public $workingCycle;
    public $chargeType;
    public $chargeRate;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $contractName;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'customerId' => self::CUSTOMER_ID,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'inTime' => self::IN_TIME,
        'outTime' => self::OUT_TIME,
        'workingHours' => self::WORKING_HOURS,
        'workingCycle' => self::WORKING_CYCLE,
        'chargeType' => self::CHARGE_TYPE,
        'chargeRate' => self::CHARGE_RATE,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'contractName' => self::CONTRACT_NAME,
    ];

}
