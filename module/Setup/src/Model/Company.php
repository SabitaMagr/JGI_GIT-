<?php
namespace Setup\Model;

class Company extends Model
{
    public $companyId;
    public $companyCode;
    public $companyName;
    public $address;
    public $telephone;
    public $fax;
    public $swift;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'companyId' => 'COMPANY_ID',
        'companyCode' => 'COMPANY_CODE',
        'companyName' => 'COMPANY_NAME',
        'address' => 'ADDRESS',
        'telephone' => 'TELEPHONE',
        'fax' => 'FAX',
        'swift' => 'SWIFT',
        'createdDt' => 'CREATED_DT',
        'modifiedDt' => 'MODIFIED_DT'
    ];

}
