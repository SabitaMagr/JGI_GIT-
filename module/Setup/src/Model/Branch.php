<?php
namespace Setup\Model;


use Application\Model\Model;

class Branch extends Model
{
    const TABLE_NAME="HR_BRANCHES";

    const BRANCH_ID="BRANCH_ID";
    const BRANCH_CODE="BRANCH_CODE";
    const BRANCH_NAME="BRANCH_NAME";
    const STREET_ADDRESS="STREET_ADDRESS";
    const COUNTRY_ID="COUNTRY_ID";
    const TELEPHONE="TELEPHONE";
    const FAX="FAX";
    const EMAIL="EMAIL";
    const REMARKS="REMARKS";
    const STATUS="STATUS";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";


    public $branchId;
    public $branchCode;
    public $branchName;
    public $streetAddress;
    public $countryId;
    public $telephone;
    public $fax;
    public $email;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;




    public $mappings = [
        'branchId'=>self::BRANCH_ID,
        'branchCode'=>self::BRANCH_CODE,
        'branchName'=>self::BRANCH_NAME,
        'streetAddress'=>self::STREET_ADDRESS,
        'countryId'=>self::COUNTRY_ID,
        'telephone'=>self::TELEPHONE,
        'fax'=>self::FAX,
        'email'=>self::EMAIL,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];



}