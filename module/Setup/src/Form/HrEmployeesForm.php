<?php
namespace Setup\Form;


use Zend\Form\Annotation;

class HrEmployeesForm
{
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Employee Code"})
     * @Annotation\Attributes({ "id":"form-employeeCode", "class":"form-employeeCode form-control" })
     */
    public $employeeCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"form-firstName", "class":"form-firstName form-control" })
     */
    public $firstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"form-middleName", "class":"form-middleName form-control" })
     */
    public $middleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"form-lastName", "class":"form-lastName form-control" })
     */
    public $lastName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Name in Nepali"})
     * @Annotation\Attributes({ "id":"form-nameNepali", "class":"form-nameNepali form-control" })
     */
    public $nameNepali;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Gender"})
     * @Annotation\Attributes({ "id":"form-genderId", "class":"form-genderId form-control" })
     */
    public $genderId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Birth Date"})
     * @Annotation\Attributes({ "id":"form-birthDate", "class":"form-birthDate form-control" })
     */
    public $birthDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Blood Group"})
     * @Annotation\Attributes({ "id":"form-bloodGroupId", "class":"form-bloodGroupId form-control" })
     */
    public $bloodGroupId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Attributes({ "id":"form-religionId", "class":"form-religionId form-control" })
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Religion"})
     */
    public $religionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Activity"})
     * @Annotation\Attributes({ "id":"form-socialActivity", "class":"form-socialActivity form-control" })
     */
    public $socialActivity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone No"})
     * @Annotation\Attributes({ "id":"form-telephoneNo", "class":"form-telephoneNO form-control" })
     */
    public $telephoneNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mobile No"})
     * @Annotation\Attributes({ "id":"form-mobileNo", "class":"form-mobileNo form-control" })
     */
    public $mobileNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Extension Number"})
     * @Annotation\Attributes({ "id":"form-extensionNo", "class":"form-extensionNo form-control" })
     */
    public $extensionNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Official"})
     * @Annotation\Attributes({ "id":"form-emailOfficial", "class":"form-emailOfficial form-control" })
     */
    public $emailOfficial;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Personal"})
     * @Annotation\Attributes({ "id":"form-emailPersonal", "class":"form-emailPersonal form-control" })
     */
    public $emailPersonal;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Network"})
     * @Annotation\Attributes({ "id":"form-socialNetwork", "class":"form-socialNetwork form-control" })
     */
    public $socialNetwork;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Name"})
     * @Annotation\Attributes({ "id":"form-emergContactName", "class":"form-emergContactName form-control" })
     */
    public $emergContactName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact No"})
     * @Annotation\Attributes({ "id":"form-emergContactNo", "class":"form-emergContactNo form-control" })
     */
    public $emergContactNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Address"})
     * @Annotation\Attributes({ "id":"form-emergContactAddress", "class":"form-emergContactAddress form-control" })
     */
    public $emergContactAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Relationship"})
     */
    public $emergContactRelationship;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent House No"})
     */
    public $addrPermHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent Ward No"})
     */
    public $addrPermWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent Street Address"})
     */
    public $addrPermStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent VDC or Municipality"})
     */
    public $addrPermVdcMunicipalityId;



    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent District"})
     *
     */
    public $addrPermDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent Zone"})
     */
    public $addrPermZoneId;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary House No"})
     */
    public $addrTempHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary Ward No"})
     */
    public $addrTempWardNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary Street Address"})
     */
    public $addrTempStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary VDC or Municipality"})
     */
    public $addrTempVdcMunicipalityId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary District"})
     */
    public $addrTempDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary Zone"})
     */
    public $addrTempZoneId;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Father Name"})
     */
    public $famFatherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Father Occupation"})
     */
    public $famFatherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mother Name"})
     */
    public $famMotherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mother Occupation"})
     */
    public $famMotherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grand Father Name"})
     */
    public $famGrandFatherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grand Mother Name"})
     */
    public $famGrandMotherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Maritual Status"})
     */
    public $maritualStatus;



    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Name"})
     */
    public $famSpouseName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Occupation"})
     */
    public $famSpouseOccupation;


    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Birth Date"})
     */
    public $famSpouseBirthDate;


    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Wdding Anniversary"})
     */
    public $famSpouseWeddingAnniversary;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Card No"})
     */
    public $idCardNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Lb rf"})
     */
    public $idLbrf;



    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Bar code"})
     */
    public $idBarCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Provident Fund No"})
     */
    public $idProvidentFundNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License No"})
     */
    public $idDrivingLicenseNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Type"})
     */
    public $idDrivingLicenseType;

    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Expiry"})
     */
    public $idDrivingLicenseExpiry;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Thumb Id"})
     */
    public $idThumbId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Pan No"})
     */
    public $idPanNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Account Id"})
     */
    public $idAccountId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Retirement No"})
     */
    public $idRetirementNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No"})
     */
    public $idCitizenshipNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Date"})
     */
    public $idCitizenshipIssueDate;



    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Place"})
     */
    public $idCitizenshipIssuePlace;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport No"})
     */
    public $idPassportNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport Expiry"})
     */
    public $idPassportExpiry;

    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Join Date"})
     */
    public $joinDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary"})
     */
    public $salary;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary PF"})
     */
    public $salaryPf;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
     */
    public $submit;

}