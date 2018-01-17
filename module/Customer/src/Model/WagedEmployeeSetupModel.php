<?php

namespace Customer\Model;

use Application\Model\Model;

class WagedEmployeeSetupModel extends Model {

    CONST TABLE_NAME = "HRIS_WAGED_EMPLOYEE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";

    public $employeeId;

    const EMPLOYEE_CODE = "EMPLOYEE_CODE";

    public $employeeCode;

    const FIRST_NAME = 'FIRST_NAME';

    public $firstName;

    const MIDDLE_NAME = 'MIDDLE_NAME';

    public $middleName;

    const LAST_NAME = 'LAST_NAME';

    public $lastName;

    const FULL_NAME = "FULL_NAME";

    public $fullName;

    const GENDER_ID = 'GENDER_ID';

    public $genderId;

    const BLOOD_GROUP_ID = "BLOOD_GROUP_ID";

    public $bloodGroupId;

    const TELEPHONE_NO = 'TELEPHONE_NO';

    public $telephoneNo;

    const MOBILE_NO = 'MOBILE_NO';

    public $mobileNo;

    const EMAIL = "EMAIL";

    public $email;

    const ID_CITIZENSHIP_NO = "ID_CITIZENSHIP_NO";

    public $idCitizenshipNo;

    const ID_CITIZENSHIP_ISSUE_DATE = "ID_CITIZENSHIP_ISSUE_DATE";

    public $idCitizenshipIssueDate;

    const ID_CITIZENSHIP_ISSUE_PLACE = "ID_CITIZENSHIP_ISSUE_PLACE";

    public $idCitizenshipIssuePlace;

    const ADDR_PERM_ZONE_ID = "ADDR_PERM_ZONE_ID";

    public $addrPermZoneId;

    const ADDR_PERM_DISTRICT_ID = "ADDR_PERM_DISTRICT_ID";

    public $addrPermDistrictId;

    const ADDR_TEMP_ZONE_ID = "ADDR_TEMP_ZONE_ID";

    public $addrTempZoneId;

    const ADDR_TEMP_DISTRICT_ID = "ADDR_TEMP_DISTRICT_ID";

    public $addrTempDistrictId;

    const STATUS = "STATUS";

    public $status;

    const CREATED_BY = "CREATED_BY";

    public $createdBy;

    const CREATED_DT = "CREATED_DT";

    public $createdDt;

    const MODIFIED_BY = "MODIFIED_BY";

    public $modifiedBy;

    const MODIFIED_DT = "MODIFIED_DT";

    public $modifiedDt;

    const REMARKS = "REMARKS";

    public $remarks;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'employeeCode' => self::EMPLOYEE_CODE,
        'firstName' => self::FIRST_NAME,
        'middleName' => self::MIDDLE_NAME,
        'lastName' => self::LAST_NAME,
        'fullName' => self::FULL_NAME,
        'genderId' => self::GENDER_ID,
        'bloodGroupId' => self::BLOOD_GROUP_ID,
        'telephoneNo' => self::TELEPHONE_NO,
        'mobileNo' => self::MOBILE_NO,
        'email' => self::EMAIL,
        'idCitizenshipNo' => self::ID_CITIZENSHIP_NO,
        'idCitizenshipIssueDate' => self::ID_CITIZENSHIP_ISSUE_DATE,
        'idCitizenshipIssuePlace' => self::ID_CITIZENSHIP_ISSUE_PLACE,
        'addrPermZoneId' => self::ADDR_PERM_ZONE_ID,
        'addrPermDistrictId' => self::ADDR_PERM_DISTRICT_ID,
        'addrTempZoneId' => self::ADDR_TEMP_ZONE_ID,
        'addrTempDistrictId' => self::ADDR_TEMP_DISTRICT_ID,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS
    ];

}
