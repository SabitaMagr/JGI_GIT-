<?php

namespace Customer\Model;

use Application\Model\Model;

class ContractAbsentDetailsModel extends Model {

    CONST TABLE_NAME = "HRIS_CONTRACT_EMP_ABSENT_SUB";
    CONST ID = "ID";
    CONST ATTENDANCE_DATE = "ATTENDANCE_DATE";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    CONST EMPLOYEE_LOCATION_ID = "EMPLOYEE_LOCATION_ID";
    CONST EMPLOYEE_DESIGNATION_ID = "EMPLOYEE_DESIGNATION_ID";
    CONST EMPLOYEE_SHIFT_ID = "EMPLOYEE_SHIFT_ID";
    CONST ABSENT_REASON = "ABSENT_REASON";
    CONST SUB_EMPLOYEE_ID = "SUB_EMPLOYEE_ID";
    CONST SUB_LOCATION_ID = "SUB_LOCATION_ID";
    CONST SUB_DESIGNATION_ID = "SUB_DESIGNATION_ID";
    CONST SUB_SHIFT_TYPE_ID = "SUB_SHIFT_TYPE_ID";
    CONST POSTING_TYPE = "POSTING_TYPE";
    CONST NORMAL_HOUR = "NORMAL_HOUR";
    CONST OT_HOUR = "OT_HOUR";
    CONST PT_HOUR = "PT_HOUR";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";

    public $id;
    public $attendanceDate;
    public $customerId;
    public $contractId;
    public $employeeId;
    public $employeeLocationId;
    public $employeeDesignationId;
    public $employeeShiftId;
    public $absentReason;
    public $subEmployeeId;
    public $subLocationId;
    public $subDesignationId;
    public $subShiftTypeId;
    public $postingType;
    public $normalHour;
    public $otHour;
    public $ptHour;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $mappings = [
        'id' => self::ID,
        'attendanceDate' => self::ATTENDANCE_DATE,
        'customerId' => self::CUSTOMER_ID,
        'contractId' => self::CONTRACT_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'employeeLocationId' => self::EMPLOYEE_LOCATION_ID,
        'employeeDesignationId' => self::EMPLOYEE_DESIGNATION_ID,
        'employeeShiftId' => self::EMPLOYEE_SHIFT_ID,
        'absentReason' => self::ABSENT_REASON,
        'subEmployeeId' => self::SUB_EMPLOYEE_ID,
        'subLocationId' => self::SUB_LOCATION_ID,
        'subDesignationId' => self::SUB_DESIGNATION_ID,
        'subShiftTypeId' => self::SUB_SHIFT_TYPE_ID,
        'postingType' => self::POSTING_TYPE,
        'normalHour' => self::NORMAL_HOUR,
        'otHour' => self::OT_HOUR,
        'ptHour' => self::PT_HOUR,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
