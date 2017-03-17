<?php

namespace Setup\Model;

use Application\Model\Model;

class Position extends Model {

    const TABLE_NAME = "HRIS_POSITIONS";
    const POSITION_ID = "POSITION_ID";
    const POSITION_NAME = "POSITION_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $positionId;
    public $positionName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $mappings = [
        'positionId' => self::POSITION_ID,
        'positionName' => self::POSITION_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
    ];

}
