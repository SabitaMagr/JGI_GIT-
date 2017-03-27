<?php
namespace SelfService\Model;

use Application\Model\Model;

class TravelExpenseDetail extends Model{
    const TABLE_NAME ="HRIS_EMP_TRAVEL_EXPENSE_DTL";
    
    const ID = "ID";
    const TRAVEL_ID = "TRAVEL_ID";
    const DEPARTURE_DATE = "DEPARTURE_DATE";
    const DEPARTURE_TIME = "DEPARTURE_TIME";
    const DEPARTURE_PLACE = "DEPARTURE_PLACE";
    const DESTINATION_DATE = "DESTINATION_DATE";
    const DESTINATION_TIME = "DESTINATION_TIME";
    const DESTINATION_PLACE = "DESTINATION_PLACE";
    const TRANSPORT_TYPE = "TRANSPORT_TYPE";
    const FARE = "FARE";
    const ALLOWANCE = "ALLOWANCE";
    const LOCAL_CONVEYENCE = "LOCAL_CONVEYENCE";
    const MISC_EXPENSES = "MISC_EXPENSES";
    const TOTAL_AMOUNT = "TOTAL_AMOUNT";
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
    
    public $id;
    public $travelId;
    public $departureDate;
    public $departureTime;
    public $departurePlace;
    public $destinationDate;
    public $destinationTime;
    public $destinationPlace;
    public $transportType;
    public $fare;
    public $allowance;
    public $localConveyence;
    public $miscExpenses;
    public $totalAmount;
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
        'id'=>self::ID,
        'travelId'=>self::TRAVEL_ID,
        'departureDate'=>self::DEPARTURE_DATE,
        'departureTime'=>self::DEPARTURE_TIME,
        'departurePlace'=>self::DEPARTURE_PLACE,
        'destinationDate'=>self::DESTINATION_DATE,
        'destinationPlace'=>self::DESTINATION_PLACE,
        'destinationTime'=>self::DESTINATION_TIME,
        'transportType'=>self::TRANSPORT_TYPE,
        'fare'=>self::FARE,
        'allowance'=>self::ALLOWANCE,
        'localConveyence'=>self::LOCAL_CONVEYENCE,
        'miscExpenses'=>self::MISC_EXPENSES,
        'totalAmount'=>self::TOTAL_AMOUNT,
        'remarks'=>self::REMARKS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'checked'=>self::CHECKED,
        'approved'=>self::APPROVED,
        'status'=>self::STATUS
    ];
}

