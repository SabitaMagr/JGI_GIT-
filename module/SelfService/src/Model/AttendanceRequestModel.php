<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/6/16
 * Time: 3:05 PM
 */
namespace SelfService\Model;

use Application\Model\Model;

class AttendanceRequestModel extends Model  {
    const TABLE_NAME="HR_ATTENDANCE_REQUEST";

    const ID="ID";
    const EMPLOYEE_ID="EMPLOYEE_ID";
    const ATTENDANCE_DT="ATTENDANCE_DT";
    const IN_TIME="IN_TIME";
    const OUT_TIME="OUT_TIME";
    const IN_REMARKS="IN_REMARKS";
    const OUT_REMARKS="OUT_REMARKS";
    const TOTAL_HOUR="TOTAL_HOUR";
    const STATUS="STATUS";
    const APPROVED_BY="APPROVED_BY";
    const APPROVED_DT="APPROVED_DT";
    const REQUESTED_DT="REQUESTED_DT";
    const APPROVED_REMARKS="APPROVED_REMARKS";

    public $id;
    public $employeeId;
    public $attendanceDt;
    public $inTime;
    public $outTime;
    public $inRemarks;
    public $outRemarks;
    public $totalHour;
    public $status;
    public $approvedBy;
    public $approvedDt;
    public $requestedDt;
    public $approvedRemarks;

    public $mappings=[
        'id'=>self::ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'attendanceDt'=>self::ATTENDANCE_DT,
        'inTime'=>self::IN_TIME,
        'outTime'=>self::OUT_TIME,
        'inRemarks'=>self::IN_REMARKS,
        'outRemarks'=>self::OUT_REMARKS,
        'totalHour'=>self::TOTAL_HOUR,
        'status'=>self::STATUS,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDt'=>self::APPROVED_DT,
        'requestedDt'=>self::REQUESTED_DT,
        'approvedRemarks'=>self::APPROVED_REMARKS
    ];
}