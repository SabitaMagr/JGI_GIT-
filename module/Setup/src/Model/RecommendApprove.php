<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/3/16
 * Time: 1:04 PM
 */
namespace Setup\Model;
use Application\Model\Model;

class RecommendApprove extends  Model{

    const TABLE_NAME="HR_RECOMMENDER_APPROVER";

    const EMPLOYEE_ID="EMPLOYEE_ID";
    const RECOMMEND_BY="RECOMMEND_BY";
    const APPROVED_BY="APPROVED_BY";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";
    const STATUS="STATUS";

    public $employeeId;
    public $recommendBy;
    public $approvedBy;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings =[
        'employeeId'=>self::EMPLOYEE_ID,
        'recommendBy'=>self::RECOMMEND_BY,
        'approvedBy'=>self::APPROVED_BY,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'status'=>self::STATUS,
    ];
}