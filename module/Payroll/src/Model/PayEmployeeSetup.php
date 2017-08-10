<?php

namespace Payroll\Model;

use Application\Model\Model;

class PayEmployeeSetup extends Model {

    const TABLE_NAME = "HRIS_PAY_EMPLOYEE_SETUP";
    const PAY_ID = "PAY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";

    public $payId;
    public $employeeId;
    public $mappings = [
        'payId' => self::PAY_ID,
        'employeeId' => self::EMPLOYEE_ID
    ];

}
