<?php

namespace Setup\Model;

use Application\Model\Model;

class HrEmployees extends Model
{
    public $employeeId;
    public $companyId;
    public $employeeCode;
    public $firstName;
    public $middleName;

    public $lastName;
    public $nameNepali;
    public $genderId;
    public $birthDate;
    public $bloodGroupId;

    public $religionId;
    public $socialActivity;
    public $telephoneNo;
    public $mobileNo;
    public $extensionNo;

    public $emailOfficial;
    public $emailPersonal;
    public $socialNetwork;
    public $emergContactName;
    public $emergContactNo;

    public $emergContactAddress;
    public $emergContactRelationship;
    public $addrPermHouseNo;
    public $addrPermWardNo;
    public $addrPermStreetAddress;
    public $addrPermCountryId;
//25
    public $addrPermVdcMunicipalityId;
    public $addrTempHouseNo;
    public $addrTempWardNo;
    public $addrTempStreetAddress;
    public $addrTempCountryId;

    public $addrTempVdcMunicipalityId;
    public $famFatherName;
    public $famFatherOccupation;
    public $famMotherName;

    public $famMotherOccupation;
    public $famGrandFatherName;
    public $famGrandMotherName;
    public $maritualStatus;
    public $famSpouseName;

    public $famSpouseOccupation;
    public $famSpouseBirthDate;
    public $famSpouseWeddingAnniversary;
    public $idCardNo;
    public $idLbrf;

    public $idBarCode;
    public $idProvidentFundNo;
    public $idDrivingLicenseNo;
    public $idDrivingLicenseType;
    public $idDrivingLicenseExpiry;

    public $idThumbId;
    public $idPanNo;
    public $idAccountId;
    public $idRetirementNo;
    public $idCitizenshipNo;

    public $idCitizenshipIssueDate;
    public $idCitizenshipIssuePlace;
    public $idPassportNo;
    public $idPassportExpiry;
    public $joinDate;

    public $salary;
    public $salaryPf;
    public $remarks;
    public $status;
    public $createdDt;

    public $countryId;


    public $mappings=[
        'employeeId'=>'EMPLOYEE_ID',
        'companyId'=>'COMPANY_ID',
        'employeeCode'=>'EMPLOYEE_CODE',
        'firstName'=>'FIRST_NAME',
        'middleName'=>'MIDDLE_NAME',
        'lastName'=>'LAST_NAME',

        'nameNepali'=>'NAME_NEPALI',
        'genderId'=>'GENDER_ID',
        'birthDate'=>'BIRTH_DATE',
        'bloodGroupId'=>'BLOOD_GROUP_ID',
        'religionId'=>'RELIGION_ID',

        'socialActivity'=>'SOCIAL_ACTIVITY',
        'telephoneNo'=>'TELEPHONE_NO',
        'mobileNo'=>'MOBILE_NO',
        'extensionNo'=>'EXTENSION_NO',
        'emailOfficial'=>'EMAIL_OFFICIAL',

        'emailPersonal'=>'EMAIL_PERSONAL',
        'socialNetwork'=>'SOCIAL_NETWORK',
        'emergContactName'=>'EMRG_CONTACT_NAME',
        'emergContactNo'=>'EMERG_CONTACT_NO',
        'emergContactAddress'=>'EMERG_CONTACT_ADDRESS',

        'emergContactRelationship'=>'EMERG_CONTACT_RELATIONSHIP',
        'addrPermHouseNo'=>'ADDR_PERM_HOUSE_NO',
        'addrPermWardNo'=>'ADDR_PERM_WARD_NO',
        'addrPermStreetAddress'=>'ADDR_PERM_STREET_ADDRESS',
        'addrPermVdcMunicipalityId'=>'ADDR_PERM_VDC_MUNICIPALITY_ID',

        'addrTempHouseNo'=>'ADDR_TEMP_HOUSE_NO',
        'addrTempWardNo'=>'ADDR_TEMP_WARD_NO',
        'addrTempStreetAddress'=>'ADDR_TEMP_STREET_ADDRESS',

        'addrTempVdcMunicipalityId'=>'ADDR_TEMP_VDC_MUNICIPALITY_ID',
        'famFatherName'=>'FAM_FATHER_NAME',
        'famFatherOccupation'=>'FAM_FATHER_OCCUPATION',

        'famMotherName'=>'FAM_MOTHER_NAME',
        'famMotherOccupation'=>'FAM_MOTHER_OCCUPATION',
        'famGrandFatherName'=>'FAM_GRAND_FATHER_NAME',
        'famGrandMotherName'=>'FAM_GRAND_MOTHER_NAME',
        'maritualStatus'=>'MARITAL_STATUS',

        'famSpouseName'=>'FAM_SPOUSE_NAME',
        'famSpouseOccupation'=>'FAM_SPOUSE_OCCUPATION',
        'famSpouseBirthDate'=>'FAM_SPOUSE_BIRTH_DATE',
        'famSpouseWeddingAnniversary'=>'FAM_SPOUSE_WEDDING_ANNIVERSARY',
        'idCardNo'=>'ID_CARD_NO',

        'idLbrf'=>'ID_LBRF',
        'idBarCode'=>'ID_BAR_CODE',
        'idProvidentFundNo'=>'ID_PROVIDENT_FUND_NO',
        'idDrivingLicenseNo'=>'ID_DRIVING_LICENCE_NO',
        'idDrivingLicenseType'=>'ID_DRIVING_LICENCE_TYPE',

        'idDrivingLicenseExpiry'=>'ID_DRIVING_LICENCE_EXPIRY',
        'idThumbId'=>'ID_THUMB_ID',
        'idPanNo'=>'ID_PAN_NO',
        'idAccountId'=>'ID_ACCOUNT_NO',
        'idRetirementNo'=>'ID_RETIREMENT_NO',

        'idCitizenshipNo'=>'ID_CITIZENSHIP_NO',
        'idCitizenshipIssueDate'=>'ID_CITIZENSHIP_ISSUE_DATE',
        'idCitizenshipIssuePlace'=>'ID_CITIZENSHIP_ISSUE_PLACE',
        'idPassportNo'=>'ID_PASSPORT_NO',
        'idPassportExpiry'=>'ID_PASSPORT_EXPIRY',

        'joinDate'=>'JOIN_DATE',
        'salary'=>'SALARY',
        'salaryPf'=>'SALARY_PF',
        'remarks'=>'REMARKS',
        'status'=>'STATUS',

        'createdDt'=>'CREATED_DT',
        'countryId'=>'COUNTRY_ID',
        'addrPermCountryId'=>'ADDR_PERM_COUNTRY_ID',
        'addrTempCountryId'=>'ADDR_TEMP_COUNTRY_ID'
    ];



}

