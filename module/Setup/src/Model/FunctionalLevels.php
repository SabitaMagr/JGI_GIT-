<?php
namespace Setup\Model;

use Application\Model\Model;


class FunctionalLevels extends Model {

    const TABLE_NAME = "HRIS_FUNCTIONAL_LEVELS";
    const FUNCTIONAL_LEVEL_ID = "FUNCTIONAL_LEVEL_ID";
    const FUNCTIONAL_LEVEL_CODE = "FUNCTIONAL_LEVEL_NO";
    const FUNCTIONAL_LEVEL_EDESC = "FUNCTIONAL_LEVEL_EDESC";
    const FUNCTIONAL_LEVEL_LDESC = "FUNCTIONAL_LEVEL_LDESC";
    const FUNCTIONAL_TYPE_ID = "FUNCTIONAL_TYPE_ID";

    public $functionalLevelsId;
    public $functionalLevelsCode;
    public $functionalLevelsEdesc;
    public $functionalLevelsLdesc;
    public $functionalTypesId;
    
    public $mappings = [
        '$functionalLevelsId' => self::FUNCTIONAL_LEVEL_ID,
        '$functionalLevelsCode' => self::FUNCTIONAL_LEVEL_CODE,
        '$functionalLevelsEdesc' => self::FUNCTIONAL_LEVEL_EDESC,
        '$functionalLevelsLdesc' => self::FUNCTIONAL_LEVEL_LDESC,
        '$functionalTypesId' => self::FUNCTIONAL_TYPE_ID,
    ];

}
