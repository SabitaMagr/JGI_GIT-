<?php

namespace Setup\Model;


use Application\Model\Model;


class EmpCurrentPosting extends Model
{
    const TABLE_NAME="HR_EMPLOYEE_CURRENT_POSTING";

    const EMPLOYEE_ID="EMPLOYEE_ID";
    const SERVICE_TYPE_ID="SERVICE_TYPE_ID";
    const BRANCH_ID="BRANCH_ID";
    const DEPARTMENT_ID="DEPARTMENT_ID";
    const DESIGNATION_ID="DESIGNATION_ID";
    const POSITION_ID="POSITION_ID";

    public $employeeId;
    public $serviceTypeId;
    public $branchId;
    public $departmentId;
    public $designationId;
    public $positionId;


    public $mappings = [
        'employeeId'=>self::EMPLOYEE_ID,
        'serviceTypeId'=>self::SERVICE_TYPE_ID,
        'branchId'=>self::BRANCH_ID,
        'departmentId'=>self::DEPARTMENT_ID,
        'designationId'=>self::DESIGNATION_ID,
        'positionId'=>self::POSITION_ID
    ];

}