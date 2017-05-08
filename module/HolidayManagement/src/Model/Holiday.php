<?php

namespace HolidayManagement\Model;

use Application\Model\Model;

class Holiday extends Model {

    const TABLE_NAME = "HRIS_HOLIDAY_MASTER_SETUP";
    const HOLIDAY_ID = "HOLIDAY_ID";
    const HOLIDAY_CODE = "HOLIDAY_CODE";
    const HOLIDAY_ENAME = "HOLIDAY_ENAME";
    const HOLIDAY_LNAME = "HOLIDAY_LNAME";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const HALFDAY = "HALFDAY";
    const FISCAL_YEAR = "FISCAL_YEAR";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $holidayId;
    public $holidayCode;
    public $holidayEname;
    public $holidayLname;
    public $startDate;
    public $endDate;
    public $halfday;
    public $fiscalYear;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $createdBy;
    public $modifiedBy;
    public $mappings = [
        'holidayId' => self::HOLIDAY_ID,
        'holidayCode' => self::HOLIDAY_CODE,
        'holidayEname' => self::HOLIDAY_ENAME,
        'holidayLname' => self::HOLIDAY_LNAME,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'halfday' => self::HALFDAY,
        'fiscalYear' => self::FISCAL_YEAR,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
    ];

}
