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
    const END_DATE="END_DATE";
    const NO_OF_DAYS="NO_OF_DAYS";
    const STATUS="STATUS";
    const RECOMMENDED_BY="RECOMMENDED_BY";
    const RECOMMENDED_DT="RECOMMENDED_DT";
    const APPROVED_BY="APPROVED_BY";
    const APPROVED_DT="APPROVED_DT";
    const HALF_DAY="HALF_DAY";
    const REQUESTED_DT="REQUESTED_DT";
    const ID="ID";
    const REMARKS="REMARKS";
    const RECOMMENDED_REMARKS="RECOMMENDED_REMARKS";
    const APPROVED_REMARKS="APPROVED_REMARKS";

    public $id;
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
    public $remarks;
    public $recommendedRemarks;
    public $approvedRemarks;

    public $mappings=[
        'id'=>self::ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'leaveId'=>self::LEAVE_ID,
        'startDate'=>self::START_DATE,
        'endDate'=>self::END_DATE,
        'noOfDays'=>self::NO_OF_DAYS,
        'status'=>self::STATUS,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDt'=>self::RECOMMENDED_DT,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDt'=>self::APPROVED_DT,
        'halfDay'=>self::HALF_DAY,
        'requestedDt'=>self::REQUESTED_DT,
        'remarks'=>self::REMARKS,
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,
        'approvedRemarks'=>self::APPROVED_REMARKS
    ];

}