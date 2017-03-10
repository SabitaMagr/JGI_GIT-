<?php
namespace Appraisal\Model;

use Application\Model\Model;

class AppraisalAssign extends Model{
    const TABLE_NAME = "HR_APPRAISAL_ASSIGN";
    
    const APPRAISAL_ID = "APPRAISAL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const APPRAISER_ID = "APPRAISER_ID";
    const REVIEWER_ID = "REVIEWER_ID";
    const REMARKS = "REMARKS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const CHECKED = "CHECKED";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED = "APPROVED";
    const STATUS = "STATUS";

    public $appraisalId;
    public $employeeId;
    public $appraiserId;
    public $reviewerId;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $checked;
    public $approvedBy;
    public $approvedDate;
    public $approved;
    public $status;
    
    public $mappings = [
        'appraisalId'=>self::APPRAISAL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'appraiserId'=>self::APPRAISER_ID,
        'reviewerId'=>self::REVIEWER_ID,
        'remarks'=>self::REMARKS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'checked'=>self::CHECKED,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approved'=>self::APPROVED,
        'status'=>self::STATUS
    ];
}