<?php
namespace Setup\Model;

use Application\Model\Model;

class Position extends Model
{
    public $positionId;
    public $positionName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;


    public $mappings = [
        'positionId' => 'POSITION_ID',
        'positionName' => 'POSITION_NAME',
        'remarks' => 'REMARKS',
        'status' => 'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];

}