<?php

namespace Setup\Model;

use Application\Model\Model;

class Company extends Model {

    const TABLE_NAME = "HRIS_COMPANY";
    const COMPANY_ID = "COMPANY_ID";
    const COMPANY_CODE = "COMPANY_CODE";
    const COMPANY_NAME = "COMPANY_NAME";
    const ADDRESS = "ADDRESS";
    const TELEPHONE = "TELEPHONE";
    const FAX = "FAX";
    const SWIFT = "SWIFT";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const LOGO = "LOGO";

    public $companyId;
    public $companyCode;
    public $companyName;
    public $address;
    public $telephone;
    public $fax;
    public $swift;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $createdBy;
    public $modifiedBy;
    public $logo;
    public $mappings = [
        'companyId' => self::COMPANY_ID,
        'companyCode' => self::COMPANY_CODE,
        'companyName' => self::COMPANY_NAME,
        'address' => self::ADDRESS,
        'telephone' => self::TELEPHONE,
        'fax' => self::FAX,
        'swift' => self::SWIFT,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'logo' => self::LOGO
    ];

}
