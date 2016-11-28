<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/4/16
 * Time: 11:51 AM
 */

namespace Payroll\Model;


use Application\Model\Model;

class MonthlyValueDetail extends Model
{
    CONST TABLE_NAME="HR_MONTHLY_VALUE_DETAIL";
    CONST MTH_ID="MTH_ID";
    CONST EMPLOYEE_ID="EMPLOYEE_ID";
    CONST MTH_VALUE="MTH_VALUE";
    CONST BRANCH_ID="BRANCH_ID";
    CONST CREATED_DT="CREATED_DT";
    CONST MODIFIED_DT="MODIFIED_DT";
    CONST STATUS="STATUS";
    CONST REMARKS="REMARKS";
    CONST COMPANY_ID="COMPANY_ID";

    public $mthId;
    public $employeeId;
    public $mthValue;
    public $branchId;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $companyId;

    public $mappings=[
        'mthId'=>self::MTH_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'mthValue'=>self::MTH_VALUE,
        'branchId'=>self::BRANCH_ID,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
        'companyId'=>self::COMPANY_ID
    ];

}