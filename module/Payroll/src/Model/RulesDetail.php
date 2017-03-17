<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/20/16
 * Time: 10:47 AM
 */

namespace Payroll\Model;

use Application\Model\Model;

class RulesDetail extends Model {

    const TABLE_NAME = "HRIS_PAY_DETAIL_SETUP";
    const PAY_ID = "PAY_ID";
    const SR_NO = "SR_NO";
    const MNENONIC_NAME = "MNENONIC_NAME";
    const MNENONIC_TYPE = "MNENONIC_TYPE";
    const IS_MONTHLY = "IS_MONTHLY";

    public $payId;
    public $srNo;
    public $mnenonicName;
    public $mnenonicType;
    public $isMonthly;
    public $mappings = [
        'payId' => self::PAY_ID,
        'srNo' => self::SR_NO,
        'mnenonicName' => self::MNENONIC_NAME,
        'mnenonicType' => self::MNENONIC_TYPE,
        'isMonthly' => self::IS_MONTHLY,
    ];

}
