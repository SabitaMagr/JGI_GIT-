<?php
namespace SelfService\Model;

use Application\Model\Model;

class TravelRequest extends Model{
    const TABLE_NAME = "HR_EMPLOYEE_TRAVEL_REQUEST";
    const TRAVEL_ID = "TRAVEL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const FROM_DATE = "FROM_DATE";
    const TO_DATE = "TO_DATE";
    const DESTINATION = "DESTINATION";
    const PURPOSE = "PURPOSE";
    const REQUESTED_TYPE = "REQUESTED_TYPE";
    const REQUESTED_AMOUNT = "REQUESTED_AMOUNT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    
    public $travelId;
    public $employeeId;
    public $requestedDate;
    public $fromDate;
    public $toDate;
    public $destination;
    public $purpose;
    public $requestedType;
    public $requestedAmount;
    public $remarks;
    public $status;
    public $recommendedDate;
    public $recommendedBy;
    public $recommendedRemarks;
    public $approvedDate;
    public $approvedBy;
    public $approvedRemarks;
    
    public $mappings= [
        'travelId'=>self::TRAVEL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'requestedDate'=>self::REQUESTED_DATE,
        'fromDate'=>self::FROM_DATE,
        'toDate'=>self::TO_DATE,
        'destination'=>self::DESTINATION,
        'purpose'=>self::PURPOSE,
        'requestedAmount'=>self::REQUESTED_AMOUNT,
        'requestedType'=>self::REQUESTED_TYPE,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,       
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,       
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_DATE
    ];   
}
