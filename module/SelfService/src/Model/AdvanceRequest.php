<?php
namespace SelfService\Model;

use Application\Model\Model;

class AdvanceRequest extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_ADVANCE_REQUEST";
    const ADVANCE_REQUEST_ID = "ADVANCE_REQUEST_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ADVANCE_ID = "ADVANCE_ID";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const ADVANCE_DATE = "ADVANCE_DATE";
    const REQUESTED_AMOUNT = "REQUESTED_AMOUNT";
    const TERMS = "TERMS";
    const REASON = "REASON";
    const STATUS = "STATUS";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    
    public $advanceRequestId;
    public $employeeId;
    public $advanceId;
    public $requestedDate;
    public $requestedAmount;
    public $advanceDate;
    public $terms;
    public $reason;
    public $status;
    public $recommendedBy;
    public $recommendedRemarks;
    public $recommendedDate;
    public $approvedBy;
    public $approvedRemarks;
    public $approvedDate;
    
    public $mappings = [
        'advanceRequestId'=>self::ADVANCE_REQUEST_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'advanceId'=>self::ADVANCE_ID,
        'requestedDate'=>self::REQUESTED_DATE,
        'requestedAmount'=>self::REQUESTED_AMOUNT,
        'advanceDate'=>self::ADVANCE_DATE,
        'terms'=>self::TERMS,
        'reason'=>self::REASON,
        'status'=>self::STATUS,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_REMARKS
    ];
 }