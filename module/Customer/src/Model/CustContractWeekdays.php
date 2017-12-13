<?php

namespace Customer\Model;

class CustContractWeekdays {

    CONST TABLE_NAME = "HRIS_CUST_CONTRACT_WEEKDAYS";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST WEEKDAY = "WEEKDAY";

    public $contractId;
    public $weekday;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'weekday' => self::WEEKDAY,
    ];

}
