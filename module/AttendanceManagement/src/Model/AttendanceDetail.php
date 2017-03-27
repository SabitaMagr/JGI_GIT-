<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 3:40 PM
 */

namespace AttendanceManagement\Model;

use Application\Model\Model;

class AttendanceDetail extends Model {

    const TABLE_NAME = "HRIS_ATTENDANCE_DETAIL";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ATTENDANCE_DT = "ATTENDANCE_DT";
    const IN_TIME = "IN_TIME";
    const OUT_TIME = "OUT_TIME";
    const IN_REMARKS = "IN_REMARKS";
    const OUT_REMARKS = "OUT_REMARKS";
    const TOTAL_HOUR = "TOTAL_HOUR";
    const LEAVE_ID = "LEAVE_ID";
    const HOLIDAY_ID = "HOLIDAY_ID";
    const TRAINING_ID = "TRAINING_ID";
    const ID = "ID";
    const TRAVEL_ID = "TRAVEL_ID";
    const SHIFT_ID = "SHIFT_ID";
    const DAYOFF_FLAG = "DAYOFF_FLAG";

    public $id;
    public $employeeId;
    public $attendanceDt;
    public $inTime;
    public $outTime;
    public $inRemarks;
    public $outRemarks;
    public $totalHour;
    public $leaveId;
    public $holidayId;
    public $trainingId;
    public $travelId;
    public $shiftId;
    public $dayoffFlag;
    public $mappings = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'attendanceDt' => self::ATTENDANCE_DT,
        'inTime' => self::IN_TIME,
        'outTime' => self::OUT_TIME,
        'inRemarks' => self::IN_REMARKS,
        'outRemarks' => self::OUT_REMARKS,
        'totalHour' => self::TOTAL_HOUR,
        'leaveId' => self::LEAVE_ID,
        'holidayId' => self::HOLIDAY_ID,
        'trainingId' => self::TRAINING_ID,
        'travelId' => self::TRAVEL_ID,
        'shiftId' => self::SHIFT_ID,
        'dayoffFlag' => self::DAYOFF_FLAG
    ];

}
