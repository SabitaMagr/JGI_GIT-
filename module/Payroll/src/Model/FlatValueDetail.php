<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/14/16
 * Time: 12:09 PM
 */

namespace Payroll\Model;


use Application\Model\Model;

class FlatValueDetail extends Model
{
    const TABLE_NAME = "HR_FLAT_VALUE_DETAIL";


    const FLAT_ID = "FLAT_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const FLAT_VALUE = "FLAT_VALUE";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const COMPANY_ID = "COMPANY_ID";


    public $flatId;
    public $employeeId;
    public $flatValue;
    public $branchId;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $companyId;

    public $mappings = [
        'flatId' => self::FLAT_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'flatValue' => self::FLAT_VALUE,
        'branchId' => self::BRANCH_ID,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'companyId' => self::COMPANY_ID
    ];

}