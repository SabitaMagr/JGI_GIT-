<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/7/16
 * Time: 3:04 PM
 */

namespace LeaveManagement\Model;


use Application\Model\Model;

class LeaveMaster extends Model
{
    const TABLE_NAME="HRIS_LEAVE_MASTER_SETUP";

    const LEAVE_ID="LEAVE_ID";
    const LEAVE_CODE="LEAVE_CODE";
    const LEAVE_ENAME="LEAVE_ENAME";
    const LEAVE_LNAME="LEAVE_LNAME";
    const ALLOW_HALFDAY="ALLOW_HALFDAY";
    const DEFAULT_DAYS="DEFAULT_DAYS";
    const FISCAL_YEAR="FISCAL_YEAR";
    const CARRY_FORWARD="CARRY_FORWARD";
    const CASHABLE="CASHABLE";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";
    const STATUS="STATUS";
    const REMARKS="REMARKS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const PAID="PAID";
    const COMPANY_ID = "COMPANY_ID";
    const MAX_ACCUMULATE_DAYS = "MAX_ACCUMULATE_DAYS";

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
    public $createdBy;
    public $modifiedBy;
    public $paid;
    public $companyId;
    public $maxAccumulateDays;

    public $mappings=[
        'leaveId'=>self::LEAVE_ID,
        'leaveCode'=>self::LEAVE_CODE,
        'leaveEname'=>self::LEAVE_ENAME,
        'leaveLname'=>self::LEAVE_LNAME,
        'allowHalfday'=>self::ALLOW_HALFDAY,
        'defaultDays'=>self::DEFAULT_DAYS,
        'fiscalYear'=>self::FISCAL_YEAR,
        'carryForward'=>self::CARRY_FORWARD,
        'cashable'=>self::CASHABLE,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'paid'=>self::PAID,
        'companyId' => self::COMPANY_ID,
        'maxAccumulateDays'=>self::MAX_ACCUMULATE_DAYS
    ];


}