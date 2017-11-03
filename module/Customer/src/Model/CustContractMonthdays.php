<?php

namespace Customer\Model;

class CustContractMonthdays {

    CONST TABLENAME = "HRIS_CUST_CONTRACT_MONTHDAYS";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST MONTHDAY = "MONTHDAY";

    public $contractId;
    public $monthday;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'monthday' => self::MONTHDAY,
    ];

}
