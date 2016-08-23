<?php
namespace Setup\Model;


class Shift extends Model
{
    public $shiftId;

    public $shiftCode;

    public $shiftName;

    public $startTime;

    public $endTime;

    public $remarks;

    public $status;

    public $createdDt;

    public $modifiedDt;

    public $mappings = [
        'shiftId'=>'SHIFT_ID',
        'shiftCode'=>'SHIFT_CODE',
        'shiftName'=>'SHIFT_NAME',
        'startTime'=>'START_TIME',
        'endTime'=>'END_TIME',
        'remarks'=>'REMARKS',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];

}

