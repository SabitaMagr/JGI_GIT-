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
    CONST NORMARL_HOUR = "NORMARL_HOUR";
    CONST PT_HOUR = "PT_HOUR";
    CONST OT_HOUR = "OT_HOUR";
    CONST MONTH_CODE_ID = "MONTH_CODE_ID";
    CONST IS_ABSENT = "IS_ABSENT";
    CONST IS_SUBSTITUTE = "IS_SUBSTITUTE";

    public $contractId;
    public $attendanceDt;
    public $inTime;
    public $outTime;
    public $totalHour;
    public $employeeId;
    public $normalHour;
    public $ptHour;
    public $otHour;
    public $monthCodeId;
    public $isAbsent;
    public $isSubstitute;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'attendanceDt' => self::ATTENDANCE_DT,
        'inTime' => self::IN_TIME,
        'outTime' => self::OUT_TIME,
        'totalHour' => self::TOTAL_HOUR,
        'employeeId' => self::EMPLOYEE_ID,
        'normalHour' => self::NORMARL_HOUR,
        'ptHour' => self::PT_HOUR,
        'otHour' => self::OT_HOUR,
        'monthCodeId' => self::MONTH_CODE_ID,
        'isAbsent' => self::IS_ABSENT,
        'isSubstitute' => self::IS_SUBSTITUTE
    ];

}
