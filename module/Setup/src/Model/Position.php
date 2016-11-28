<?php
namespace Setup\Model;

use Application\Model\Model;

class Position extends Model
{
    const TABLE_NAME="HR_POSITIONS";

    const POSITION_ID="POSITION_ID";
    const POSITION_NAME="POSITION_NAME";
    const REMARKS="REMARKS";
    const STATUS="STATUS";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";

    public $positionId;
    public $positionName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;


    public $mappings = [
        'positionId' => self::POSITION_ID,
        'positionName' => self::POSITION_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];

}