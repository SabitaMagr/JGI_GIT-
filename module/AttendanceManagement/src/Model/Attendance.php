<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;
class Attendance extends Model{

    const TABLE_NAME = "HR_ATTENDANCE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ATTENDANCE_DT = "ATTENDANCE_DT";
    const IP_ADDRESS = "IP_ADDRESS";
    const ATTENDANCE_FROM = "ATTENDANCE_FROM";

    public $employeeId;
    public $attendanceDt;
    public $ipAddress;
    public $attendanceFrom;
    
    public $mappings=[
        'employeeId'=>self::EMPLOYEE_ID,
        'attendanceDt'=>self::ATTENDANCE_DT,
        'ipAddress'=>self::IP_ADDRESS,
        'attendanceFrom'=>self::ATTENDANCE_FROM
    ];
}
