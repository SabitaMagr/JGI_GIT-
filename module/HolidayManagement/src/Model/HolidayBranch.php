<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/16/16
 * Time: 12:43 PM
 */

namespace HolidayManagement\Model;

use Application\Model\Model;

class HolidayBranch extends Model {

    const TABLE_NAME="HRIS_HOLIDAY_BRANCH";
    const HOLIDAY_ID="HOLIDAY_ID";
    const BRANCH_ID="BRANCH_ID";

    public $holidayId;
    public $branchId;

    public $mappings=[
        'holidayId'=> self::HOLIDAY_ID,
        'branchId'=>self::BRANCH_ID
    ];
}