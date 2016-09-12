<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:52 AM
 */

namespace LeaveManagement\Model;


use Setup\Model\Model;

class LeaveApply extends Model
{
    public $employeeId;
    public $leaveId;
    public $startDate;
    public $endDate;
    public $noOfDays;
    public $status;
    public $recommendedBy;
    public $recommendedDt;
    public $approvedBy;
    public $approvedDt;
    public $halfDay;
    public $requestedDt;

    public $mappings=[
        'employeeId'=>'EMPLOYEE_ID',
        'leaveId'=>'LEAVE_ID',
        'startDate'=>'START_DATE',
        'endDate'=>'END_DATE',
        'noOfDays'=>'NO_OF_DAYS',
        'status'=>'STATUS',
        'recommendedBy'=>'RECOMMENDED_BY',
        'recommendedDt'=>'RECOMMENDED_DT',
        'approvedBy'=>'APPROVED_BY',
        'approvedDt'=>'APPROVED_DT',
        'halfDay'=>'HALF_DAY',
        'requestedDt'=>'REQUESTED_DT'
    ];

}