<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/11/16
 * Time: 4:55 PM
 */

namespace HolidayManagement\Model;


use Setup\Model\Model;

class Holiday extends Model
{
    public $holidayId;
    public $holidayCode;
    public $genderId;
    public $branchId;
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

    public $mappings = [
        'holidayId'=>'HOLIDAY_ID',
        'holidayCode'=>'HOLIDAY_CODE',
        'genderId'=>'GENDER_ID',
        'branchId'=>'BRANCH_ID',
        'holidayEname'=>'HOLIDAY_ENAME',
        'holidayLname'=>'HOLIDAY_LNAME',
        'startDate'=>'START_DATE',
        'endDate'=>'END_DATE',
        'halfday'=>'HALFDAY',
        'fiscalYear'=>'FISCAL_YEAR',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT',
        'status'=>'STATUS',
        'remarks'=>'REMARKS'
    ];

}