<?php

namespace Setup\Model;
use Application\Model\Model;

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/23/16
 * Time: 12:01 PM
 */

class EmpCurrentPosting extends Model
{
    public $employeeId;

    public $serviceTypeId;

    public $branchId;

    public $departmentId;

    public $designationId;

    public $positionId;


    public $mappings = [
        'employeeId'=>'EMPLOYEE_ID',
        'serviceTypeId'=>'SERVICE_TYPE_ID',
        'branchId'=>'BRANCH_ID',
        'departmentId'=>'DEPARTMENT_ID',
        'designationId'=>'DESIGNATION_ID',
        'positionId'=>'POSITION_ID'
    ];

}