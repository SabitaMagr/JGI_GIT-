<?php
namespace Setup\Model;


use Application\Model\Model;

class Shift extends Model
{
    public $shiftId;
    public $shiftCode;
    public $shiftEname;
    public $shiftLname;
    public $startTime;
    public $endTime;
    public $halfDayEndTime;
    public $halfTime;
    public $lateIn;
    public $earlyOut;
    public $startDate;
    public $endDate;
    public $weekday1;
    public $weekday2;
    public $weekday3;
    public $weekday4;
    public $weekday5;
    public $weekday6;
    public $weekday7;
    public $currentShift;
    public $twoDayShift;
    public $defaultShift;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $status;

    public $mappings = [
        'shiftId'=>'SHIFT_ID',
        'shiftCode'=>'SHIFT_CODE',
        'shiftEname'=>'SHIFT_ENAME',
        'shiftLname'=>'SHIFT_LNAME',
        'startTime'=>'START_TIME',
        'endTime'=>'END_TIME',
        'halfDayEndTime'=>'HALF_DAY_END_TIME',
        'halfTime'=>'HALF_TIME',
        'lateIn'=>'LATE_IN',
        'earlyOut'=>'EARLY_OUT',
        'startDate'=>'START_DATE',
        'endDate'=>'END_DATE',
        'weekday1'=>'WEEKDAY1',
        'weekday2'=>'WEEKDAY2',
        'weekday3'=>'WEEKDAY3',
        'weekday4'=>'WEEKDAY4',
        'weekday5'=>'WEEKDAY5',
        'weekday6'=>'WEEKDAY6',
        'weekday7'=>'WEEKDAY7',
        'currentShift'=>'CURRENT_SHIFT',
        'twoDayShift'=>'TWO_DAY_SHIFT',
        'defaultShift'=>'DEFAULT_SHIFT',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT',
        'remarks'=>'REMARKS',
        'status'=>'STATUS'
        ];

}

