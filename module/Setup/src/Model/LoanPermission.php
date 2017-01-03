<?php
namespace Setup\Model;

use Application\Model\Model;

class LoanPermission extends Model{
    const TABLE_NAME = "HR_LOAN_PERMISSION";
    const PERMISSION_ID = "PERMISSION_ID";
    const LOAN_ID = "LOAN_ID";
    const PERMISSION_TYPE = "PERMISSION_TYPE";
    const VALUE = "VALUE";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $permissionId;
    public $loanId;
    public $permissionType;
    public $value;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;
    
    public $mappings = [
        'permissionId'=>self::PERMISSION_ID,
        'permissionType'=>self::PERMISSION_TYPE,
        'loanId'=>self::LOAN_ID,
        'value'=>self::VALUE,
        'createdDate'=>self::CREATED_DATE,
        'createdBy'=>self::CREATED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'status'=>self::STATUS
    ];
}