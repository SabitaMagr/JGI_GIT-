<?php

namespace Customer\Model;

use Application\Model\Model;

class ContractAttendanceModel extends Model {

    CONST TABLE_NAME = "HRIS_CUST_CONTRACT_ATTENDANCE";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST ATTENDANCE_DT = "ATTENDANCE_DT";
    CONST IN_TIME = "IN_TIME";
    CONST OUT_TIME = "OUT_TIME";
    CONST TOTAL_HOUR = "TOTAL_HOUR";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    
    public $contractId;
    public $attendanceDt;
    public $inTime;
    public $outTime;
    public $totalHour;
    public $employeeId;
    
    
    public $mappings = [
        'contractId'=>self::CONTRACT_ID,
        'attendanceDt'=>self::ATTENDANCE_DT,
        'inTime'=>self::IN_TIME,
        'outTime'=>self::OUT_TIME,
        'totalHour'=>self::TOTAL_HOUR,
        'employeeId'=>self::EMPLOYEE_ID
    ];

}
