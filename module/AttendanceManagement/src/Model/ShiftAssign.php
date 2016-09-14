<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:49 PM
 */

namespace AttendanceManagement\Model;

use Application\Model\Model;

class ShiftAssign extends  Model {
    const TABLE_NAME="EMPLOYEE_SHIFT_ASSIGN";
    const SHIFT_ASSIGN_ID="SHIFT_ASSIGN_ID";
    const EMPLOYEE_CODE="EMPLOYEE_CODE";
    const FROM_DATE="FROM_DATE";
    const TO_DATE="TO_DATE";
    const SHIFT_CODE="SHIFT_CODE";
    const SUN="SUN";
    const MON="MON";
    const TUE="TUE";
    const WED="WED";
    const THU="THU";
    const FRI="FRI";
    const SAT="SAT";

    public $shiftAssignId;
    public $employeeCode;
    public $fromDate;
    public $toDate;
    public $shiftCode;
    public $sun;
    public $mon;
    public $tue;
    public $wed;
    public $thu;
    public $fri;
    public $sat;
}