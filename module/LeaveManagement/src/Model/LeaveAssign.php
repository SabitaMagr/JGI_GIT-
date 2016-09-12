<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:53 AM
 */

namespace LeaveManagement\Model;

use Setup\Model\Model;

class LeaveAssign extends Model
{
    public $employeeLeaveAssignId;
    public $employeeId;
    public $leaveId;
    public $previousYearBalance;
    public $totalDays;
    public $balance;
    public $fiscalYear;
    public $remarks;
    public $createdDt;
    public $modifiedDt;

    public $mappings=[
        'employeeLeaveAssignId'=>'EMPLOYEE_LEAVE_ASSIGN_ID',
        'employeeId'=>'EMPLOYEE_ID',
        'leaveId'=>'LEAVE_ID',
        'previousYearBalance'=>'PREVIOUS_YEAR_BAL',
        'totalDays'=>'TOTAL_DAYS',
        'balance'=>'BALANCE',
        'fiscalYear'=>'FISCAL_YEAR',
        'remarks'=>'REMARKS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];
}