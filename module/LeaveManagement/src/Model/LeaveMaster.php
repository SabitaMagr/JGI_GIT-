<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/7/16
 * Time: 3:04 PM
 */

namespace LeaveManagement\Model;


use Setup\Model\Model;

class LeaveMaster extends Model
{
    public $leaveId;
    public $leaveCode;
    public $leaveEname;
    public $leaveLname;
    public $allowHalfday;
    public $defaultDays;
    public $fiscalYear;
    public $carryForward;
    public $cashable;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;

    public $mappings=[
        'leaveId'=>'LEAVE_ID',
        'leaveCode'=>'LEAVE_CODE',
        'leaveEname'=>'LEAVE_ENAME',
        'leaveLname'=>'LEAVE_LNAME',
        'allowHalfday'=>'ALLOW_HALFDAY',
        'defaultDays'=>'DEFAULT_DAYS',
        'fiscalYear'=>'FISCAL_YEAR',
        'carryForward'=>'CARRY_FORWARD',
        'cashable'=>'CASHABLE',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT',
        'status'=>'STATUS',
        'remarks'=>'REMARKS'
    ];


}