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
    const TABLE_NAME="HR_EMPLOYEE_SHIFT_ASSIGN";

    const EMPLOYEE_ID="EMPLOYEE_ID";
    const SHIFT_ID="SHIFT_ID";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";
    const REMARKS="REMARKS";
    const STATUS="STATUS";

    public $employeeId;
    public $shiftId;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $status;

    public $mappings=[
        'employeeId'=>self::EMPLOYEE_ID,
        'shiftId'=>self::SHIFT_ID,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS
    ];

}