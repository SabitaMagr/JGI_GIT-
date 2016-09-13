<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:52 AM
 */

namespace LeaveManagement\Model;


use Application\Model\Model;

class LeaveApply extends Model
{
    const TABLE_NAME="HR_EMPLOYEE_LEAVE_REQUEST";
    const EMPLOYEE_ID="EMPLOYEE_ID";
    const LEAVE_ID="LEAVE_ID";
    const START_DATE="START_DATE";


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
        'employeeId'=>self::EMPLOYEE_ID,
        'leaveId'=>self::LEAVE_ID,
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