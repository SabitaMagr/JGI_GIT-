<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/31/16
 * Time: 11:41 AM
 */
namespace Setup\Form;

use Setup\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabOne extends Model
{
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Employee Code"})
     * @Annotation\Attributes({ "id":"form-employeeCode", "class":"form-employeeCode form-control" })
     */
    public $employeeCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Companies"})
     * @Annotation\Attributes({ "id":"companyId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $companyId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"form-firstName", "class":"form-control" })
     */
    public $firstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"form-middleName", "class":"form-control" })
     */
    public $middleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"form-lastName", "class":"form-control" })
     */
    public $lastName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Name in Nepali"})
     * @Annotation\Attributes({ "id":"form-nameNepali", "class":"form-control" })
     */
    public $nameNepali;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Gender"})
     * @Annotation\Attributes({ "id":"genderId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $genderId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Birth Date"})
     * @Annotation\Attributes({ "id":"employeeBirthDate", "class":"form-control" })
     */
    public $birthDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Blood Group"})
     * @Annotation\Attributes({ "id":"bloodGroupId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $bloodGroupId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Religion"})
     * @Annotation\Attributes({ "id":"religionId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $religionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Activity"})
     * @Annotation\Attributes({ "id":"socialActivity","class":"form-control" })
     */
    public $socialActivity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone No"})
     * @Annotation\Attributes({ "id":"telephoneNo", "class":"form-control" })
     * @Annotation\Required(false)
     */
    public $telephoneNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mobile No"})
     * @Annotation\Attributes({ "id":"mobileNo", "class":"form-control" })
     */
    public $mobileNo;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Extension Number"})
     * @Annotation\Attributes({ "id":"extensionNo", "class":"form-control" })
     */
    public $extensionNo;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Official"})
     * @Annotation\Attributes({ "id":"emailOfficial", "class":"form-control" })
     */
    public $emailOfficial;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Personal"})
     * @Annotation\Attributes({ "id":"emailPersonal", "class":"form-control" })
     */
    public $emailPersonal;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Network"})
     * @Annotation\Attributes({ "id":"socialNetwork", "class":"form-control" })
     */
    public $socialNetwork;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Country"})
     * @Annotation\Attributes({ "id":"countryId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $countryId;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Name"})
     * @Annotation\Attributes({ "id":"emergContactName", "class":"form-control" })
     */
    public $emergContactName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Emergency Contact No"})
     * @Annotation\Attributes({ "id":"emergContactNo", "class":"form-control" })
     */
    public $emergContactNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Emergency Contact Address"})
     * @Annotation\Attributes({ "id":"emergContactAddress", "class":"form-control" })
     */
    public $emergContactAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Emergency Contact Relationship"})
     * @Annotation\Attributes({ "id":"emergContactRelationship", "class":"form-control" })
     */
    public $emergContactRelationship;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":" Permanent House No"})
     * @Annotation\Attributes({ "id":"addrPermHouseNo", "class":"form-control" })
     */
    public $addrPermHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent Ward No"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrPermWardNo", "class":"form-control" })
     */
    public $addrPermWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":" Permanent Street Address"})
     * @Annotation\Attributes({ "id":"addrPermStreetAddress", "class":"form-control" })
     */
    public $addrPermStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent VDC or Municipality"})
     * @Annotation\Attributes({ "id":"addrPermVdcMunicipalityId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrPermVdcMunicipalityId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrPermDistrictId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     *
     */
    public $addrPermDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent Zone"})
     * @Annotation\Attributes({ "id":"addrPermZoneId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrPermZoneId;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary House No"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrTempHouseNo", "class":"form-control" })
     */
    public $addrTempHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Temporary Ward No"})
     * @Annotation\Attributes({ "id":"addrTempWardNo", "class":"form-control" })
     */
    public $addrTempWardNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary Street Address"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrTempStreetAddress", "class":"form-control" })
     */
    public $addrTempStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary VDC or Municipality"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrTempVdcMunicipality","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempVdcMunicipalityId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrTempDistrictId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary Zone"})
     * @Annotation\Attributes({ "id":"addrTempZoneId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempZoneId;

    public $addrPermCountryId;
    public $addrTempCountryId;
    public $status;
    public $createdDt;

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
        'countryId'=>'COUNTRY_ID',
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
        'addrPermCountryId'=>'ADDR_PERM_COUNTRY_ID',
        'addrTempCountryId'=>'ADDR_TEMP_COUNTRY_ID',
        'status'=>'STATUS',

        'createdDt'=>'CREATED_DT',
    ];

}
