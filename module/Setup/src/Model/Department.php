<?php
namespace Setup\Model;

use Application\Model\Model;

class Department extends Model
{
    const TABLE_NAME="HR_DEPARTMENTS";

    const DEPARTMENT_ID="DEPARTMENT_ID";
    const DEPARTMENT_CODE="DEPARTMENT_CODE";
    const DEPARTMENT_NAME="DEPARTMENT_NAME";
    const COUNTRY_ID="COUNTRY_ID";
    const PARENT_DEPARTMENT="PARENT_DEPARTMENT";
    const REMARKS="REMARKS";
    const STATUS="STATUS";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";

    public $departmentId;
    public $departmentCode;
    public $departmentName;
    public $countryId;
    public $remarks;
    public $parentDepartment;
    public $status;
    public $createdDt;
    public $modifiedDt;


    public $mappings=[
        'departmentId'=>self::DEPARTMENT_ID,
        'departmentCode'=>self::DEPARTMENT_CODE,
        'departmentName'=>self::DEPARTMENT_NAME,
        'countryId'=>self::COUNTRY_ID,
        'parentDepartment'=>self::PARENT_DEPARTMENT,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];

}
