<?php

namespace Payroll\Model;

use Application\Model\Model;

class PositionFlatValue extends Model {

    const TABLE_NAME = "HRIS_POSITION_FLAT_VALUE";
    const FLAT_ID = "FLAT_ID";
    const POSITION_ID = "POSITION_ID";
    const MONTH_ID = "MONTH_ID";
    const ASSIGNED_VALUE = "ASSIGNED_VALUE";

    public $flatId;
    public $positionId;
    public $monthId;
    public $assignedValue;
    public $mappings = [
        'flatId' => self::FLAT_ID,
        'positionId' => self::POSITION_ID,
        'monthId' => self::MONTH_ID,
        'assignedValue' => self::ASSIGNED_VALUE,
    ];

}
