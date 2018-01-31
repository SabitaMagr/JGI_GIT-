<?php

namespace Customer\Model;

use Application\Model\Model;

class CustContractEmp extends Model {

    CONST TABLE_NAME = "HRIS_CUST_CONTRACT_EMP";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    CONST START_DATE = "START_DATE";
    CONST END_DATE = "END_DATE";
    CONST ASSIGNED_DATE = "ASSIGNED_DATE";
    CONST OLD_EMPLOYEE_ID = "OLD_EMPLOYEE_ID";
    CONST START_TIME = "START_TIME";
    CONST END_TIME = "END_TIME";
    CONST WORKING_HOUR = "WORKING_HOUR";
    CONST NEPALI_MONTH = "NEPALI_MONTH";
    CONST NEPALI_YEAR = "NEPALI_YEAR";

    public $contractId;
    public $employeeId;
    public $startDate;
    public $endDate;
    public $assignedDate;
    public $oldEmployeeId;
    public $startTime;
    public $endTime;
//    public $
    
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'assignedDate' => self::ASSIGNED_DATE,
        'oldEmployeeId' => self::OLD_EMPLOYEE_ID,
    ];

}
