<?php
namespace Setup\Model;

class Department extends Model
{
    public $departmentId;
    public $departmentCode;
    public $departmentName;
    public $remarks;
    public $parentDepartment;
    public $status;
    public $createdDt;
    public $modifiedDt;


    public $mappings=[
        'departmentId'=>'DEPARTMENT_ID',
        'departmentCode'=>'DEPARTMENT_CODE',
        'departmentName'=>'DEPARTMENT_NAME',
        'parentDepartment'=>'PARENT_DEPARTMENT',
        'remarks'=>'REMARKS',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];

}
