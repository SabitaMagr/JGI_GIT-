<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 5:15 PM
 */

namespace Payroll\Model;


use Application\Model\Model;

class PayPositionSetup extends Model
{
    const TABLE_NAME="HR_PAY_POSITION_SETUP";
    const PAY_ID="PAY_ID";
    const POSITION_ID="POSITION_ID";

    public $payId;
    public $positionId;


    public $mappings=[
        'payId'=>self::PAY_ID,
        'positionId'=>self::POSITION_ID
    ];

}