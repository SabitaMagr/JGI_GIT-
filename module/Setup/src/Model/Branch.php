<?php
namespace Setup\Model;


class Branch extends Model
{
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
        'branchId'=>'BRANCH_ID',
        'branchCode'=>'BRANCH_CODE',
        'branchName'=>'BRANCH_NAME',
        'streetAddress'=>'STREET_ADDRESS',
        'countryId'=>'COUNTRY_ID',
        'telephone'=>'TELEPHONE',
        'fax'=>'FAX',
        'email'=>'EMAIL',
        'remarks'=>'REMARKS',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];



}