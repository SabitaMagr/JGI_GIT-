<?php
namespace Travel\Model;

use Application\Model\Model;

class RecommenderApprover extends Model{
    const TABLE_NAME = "HRIS_TRVL_RECOMMENDER_APPROVER";
    
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const RECOMMEND_BY = "RECOMMEND_BY";
    const APPROVED_BY = "APPROVED_BY";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $employeeId;
    public $recommendBy;
    public $approvedBy;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    
    public $mappings = [
        'employeeId'=>self::EMPLOYEE_ID,
        'recommendBy'=>self::RECOMMEND_BY,
        'approvedBy'=>self::APPROVED_BY,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedBy'=>self::MODIFIED_BY
    ];
}
