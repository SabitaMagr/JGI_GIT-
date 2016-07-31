<?php
namespace Test\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("EmployeeForm")
 */

class EmployeeForm
{

    /**
     *@Annotation\Type("Zend\Form\Element\Text")
     *@Annotation\Required({"required":"true"})
     *@Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     *@Annotation\Options({"label":"Employee Id"})
     *@Annotation\Attributes({ "id":"form-employeeId", "class":"form-employeeId form-control", "placeholder":"Employee Id..."  })
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Title", "value_options":{"M":"Mr.","F":"Mrs.","N":"Ms."}})
     * @Annotation\Attributes({ "id":"form-title","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-title form-control"})
     */
    public $title;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"form-firstName", "class":"form-firstName form-control", "placeholder":"Firt Name..."})
     */
    public $firstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"form-middleName", "class":"form-middleName form-control", "placeholder":"Middle Name..."  })
     */
    public $middleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"form-lastName", "class":"form-lastName form-control","placeholder":"Last Name.." })
     */
    public $lastName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"In Nepali"})
     * @Annotation\Attributes({ "id":"form-inNepali",  "class":"form-inNepali form-control","placeholder":"write your fullname in Nepali.."})
     */
    public $inNepali;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Date of Birth"})
     * @Annotation\Attributes({"id":"start-date", "class":"form-dateOfBirth form-control"})
     */
    public $dateOfBirth;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Place of Birth"})
     * @Annotation\Attributes({ "id":"form-placeOfBirth","class":"form-placeOfBirth form-control"})
    */
    public $placeOfBirth;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Gender", "value_options":{"F":"Female","M":"Male","O":"Others"}})
     * @Annotation\Attributes({ "id":"form-gender","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-gender form-control"})
     */
    public $gender;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Blood Group","value_options":{"A-":"A-","A+":"A+","B-":"B-","B+":"B+","AB-":"AB-","AB+":"AB+","O-":"O-","O+":"O+"}})
     * @Annotation\Attributes({ "id":"form-bloodGroup","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-control"})
    */
    public $bloodGroup;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Nationality at Birth"})
     * @Annotation\Attributes({ "id":"form-nationalityAtBirth","class":"form-nationalityAtBirth form-control"})
    */
    public $nationalityAtBirth;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Present Nationality"})
     * @Annotation\Attributes({ "id":"form-presentNationality","class":"form-presentNationality form-control"})
    */
    public $presentNationality;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Religion","value_options":{"Hindu":"Hindu","Muslim":"Muslim","Buddhist":"Buddhist","Christian":"Christian","Others":"Others"}})
     * @Annotation\Attributes({ "id":"form-religion","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-control"})
    */
    public $religion;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Social Activities"})
     * @Annotation\Attributes({ "id":"form-socialActivities", "class":"form-socialActivities form-control" })
     */
    public $socialActivities;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Extension No."})
     * @Annotation\Attributes({"id":"form-extensionNo","class":"form-extensionNo form-control"})
     */
    public $extensionNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Phone No."})
     * @Annotation\Attributes({ "id":"form-phoneNo", "class":"form-phoneNo form-control" })
     */
    public $phoneNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mobile No."})
     * @Annotation\Attributes({ "id":"form-mobileNo", "class":"form-mobileNo form-control"})
     */
    public $mobileNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Official Email"})
     * @Annotation\Attributes({ "id":"form-officialEmail", "class":"form-officialEmail form-control"})
     */
    public $officialEmail;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Personal Email"})
     * @Annotation\Attributes({ "id":"form-personalEmail", "class":"form-personalEmail form-control"})
     */
    public $personalEmail;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Social Network"})
     * @Annotation\Attributes({ "id":"form-socialNetwork", "class":"form-socialNetwork form-control"})
     */
    public $socialNetwork;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Highest Qualification"})
     * @Annotation\Attributes({ "id":"form-highestQualification", "class":"form-highestQualification form-control"})
     */
    public $highestQualification;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Name"})
     * @Annotation\Attributes({ "id":"form-contactName", "class":"form-contactName form-control"})
     */
    public $contactName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Relationship"})
     * @Annotation\Attributes({ "id":"form-relationship", "class":"form-relationship form-control" })
     */
    public $relationship;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Address"})
     * @Annotation\Attributes({ "id":"form-contactAddress", "class":"form-contactAddress form-control"})
     */
    public $contactAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Number"})
     * @Annotation\Attributes({ "id":"form-contactNumber", "class":"form-contactNumber form-control"})
     */
    public $contactNumber;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"House No."})
     * @Annotation\Attributes({ "id":"form-pHouseNo", "class":"form-pHouseNo form-control"})
     */
    public $pHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"House No."})
     * @Annotation\Attributes({ "id":"form-cHouseNo", "class":"form-cHouseNo form-control" })
     */
    public $cHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Ward No."})
     * @Annotation\Attributes({ "id":"form-pWardNo", "class":"form-pWardNo form-control"})
     */
    public $pWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Ward No"})
     * @Annotation\Attributes({ "id":"form-cWardNo", "class":"form-cWardNo form-control"})
     */
    public $cWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Street Address"})
     * @Annotation\Attributes({ "id":"form-pStreetAddress", "class":"form-pStreetAddress form-control" })
     */
    public $pStreetAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Street Address"})
     * @Annotation\Attributes({ "id":"form-cStreetAddress", "class":"form-cStreetAddress form-control"})
     */
    public $cStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"VDC/City"})
     * @Annotation\Attributes({ "id":"form-pVdcCity", "class":"form-pVdcCity form-control" })
     */
    public $pVdcCity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"VDC/City"})
     * @Annotation\Attributes({ "id":"form-cVdcCity", "class":"form-cVdcCity form-control" })
     */
    public $cVdcCity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"District"})
     * @Annotation\Attributes({ "id":"form-pDistrict", "class":"form-pDistrict form-control" })
     */
    public $pDistrict;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"District"})
     * @Annotation\Attributes({ "id":"form-cDistrict", "class":"form-cDistrict form-control" })
     */
    public $cDistrict;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Father's Name"})
     * @Annotation\Attributes({ "id":"form-fathersName", "class":"form-fathersName form-control"})
     */
    public $fathersName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Father Occupation"})
     * @Annotation\Attributes({ "id":"form-fathersOccupation", "class":"form-fathersOccupation form-control" })
     */
    public $fathersOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mother's Name"})
     * @Annotation\Attributes({ "id":"form-mothersName", "class":"form-mothersName form-control" })
     */
    public $mothersName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mother's Occupation"})
     * @Annotation\Attributes({ "id":"form-mothersOccupation", "class":"form-mothersOccupation form-control"})
     */
    public $mothersOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Grand Father Name"})
     * @Annotation\Attributes({ "id":"form-grandFatherName", "class":"form-grandFatherName form-control"})
     */
    public $grandFatherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Grand Mother Name"})
     * @Annotation\Attributes({ "id":"form-grandMotherName", "class":"form-grandMotherName form-control"})
     */
    public $grandMotherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Marital Status","value_options":{"Unmarried":"Unmarried","Married":"Married","Divorced":"Divorced","Widow":"Widow"}})
     * @Annotation\Attributes({ "id":"form-maritalStatus", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-maritalStatus form-control" })
     */
    public $maritalStatus;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Spouse Name"})
     * @Annotation\Attributes({ "id":"form-spouseName", "class":"form-spouseName form-control"})
     */
    public $spouseName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Spouse Occupation"})
     * @Annotation\Attributes({ "id":"form-spouseOccupation", "class":"form-spouseOccupation form-control"})
     */
    public $spouseOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Spouse Birth Date"})
     * @Annotation\Attributes({ "id":"form-spouseBirthDate", "class":"form-spouseBirthDate form-control"})
     */
    public $spouseBirthDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Wedding Anniversary"})
     * @Annotation\Attributes({ "id":"form-weddingAnniversary", "class":"form-weddingAnniversary form-control"})
     */
    public $weddingAnniversary;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Card Pin Id"})
     * @Annotation\Attributes({ "id":"form-cardPinId", "class":"form-cardPinId form-control"})
     */
    public $cardPinId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"LBRF"})
     * @Annotation\Attributes({ "id":"form-lbrf", "class":"form-lbrf form-control" })
     */
    public $lbrf;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Bar Code"})
     * @Annotation\Attributes({ "id":"form-barCode", "class":"form-barCode form-control"})
     */
    public $barCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Provident Fund No"})
     * @Annotation\Attributes({ "id":"form-providentFundNo", "class":"form-providentFundNo form-control"})
     */
    public $providentFundNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Driving License No"})
     * @Annotation\Attributes({ "id":"form-drivingLicenceNo", "class":"form-drivingLicenceNo form-control"})
     */
    public $drivingLicenceNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Driving License Expiry Date"})
     * @Annotation\Attributes({ "id":"form-drivingLicenceExpiryDate", "class":"form-drivingLicenceExpiryDate form-control" })
     */
    public $drivingLicenceExpiryDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Driving License Type"})
     * @Annotation\Attributes({ "id":"form-drivingLicenseType", "class":"form-drivingLicenseType form-control"})
     */
    public $drivingLicenseType;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Passport No."})
     * @Annotation\Attributes({ "id":"form-passportNo", "class":"form-passportNo form-control"})
     */
    public $passportNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Thumb Id"})
     * @Annotation\Attributes({ "id":"form-thumbId", "class":"form-thumbId form-control"})
     */
    public $thumbId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Pan No"})
     * @Annotation\Attributes({ "id":"form-panNo", "class":"form-panNo form-control"})
     */
    public $panNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Account No"})
     * @Annotation\Attributes({ "id":"form-accountNo", "class":"form-accountNo form-control"})
     */
    public $accountNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Retirement No"})
     * @Annotation\Attributes({ "id":"form-retirementNo", "class":"form-retirementNo form-control"})
     */
    public $retirementNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Citizenship No"})
     * @Annotation\Attributes({ "id":"form-citizenshipNo", "class":"form-citizenshipNo form-control"})
     */
    public $citizenshipNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Citizenship Issued Date"})
     * @Annotation\Attributes({ "id":"form-citizenshipIssuedDate", "class":"form-citizenshipIssuedDate form-control" })
     */
    public $citizenshipIssuedDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Citizenship Issued Place"})
     * @Annotation\Attributes({ "id":"form-citizenshipIssuedPlace", "class":"form-citizenshipIssuedPlace form-control" })
     */
    public $citizenshipIssuedPlace;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Passport Expiry Date"})
     * @Annotation\Attributes({ "id":"form-passportExpiryDate", "class":"form-passportExpiryDate form-control" })
     */
    public $passportExpiryDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Corporate Level","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-corporateLevel", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-corporateLevel form-control","placeholder":"" })
     */
    public $corporateLevel;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Department","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-department", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-department form-control","placeholder":"" })
     */
    public $department;



    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Funtional Title","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-functionalTitle", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-functionalTitle form-control","placeholder":"" })
     */
    public $functionalTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Grade Name","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-gradeName", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-gradeName form-control","placeholder":"" })
     */
    public $gradeName;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Job Evaluation Title","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-jobEvaluationTitle", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-jobEvaluationTitle form-control","placeholder":"" })
     */
    public $jobEvaluationTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Branch Name","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-branchName","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-branchName form-control","placeholder":"" })
     */
    public $branchName;
 
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Employee Type","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-employeeType", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-employeeType form-control","placeholder":"" })
     */
    public $employeeType ;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Seniority Level"})
     * @Annotation\Attributes({ "id":"form-seniorityLevel", "class":"form-seniorityLevel form-control","placeholder":"" })
     */
    public $seniorityLevel ;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Probation Date"})
     * @Annotation\Attributes({ "id":"form-probationDate", "class":"form-probationDate form-control","placeholder":"" })
     */
    public $probationDate;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Probation Period"})
     * @Annotation\Attributes({ "id":"form-probationPeriod", "class":"form-probationPeriod form-control","placeholder":"" })
     */
    public $probationPeriod;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Join Date"})
     * @Annotation\Attributes({ "id":"form-joinDate", "class":"form-joinDate form-control","placeholder":"" })
     */
    public $joinDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Permanent Date"})
     * @Annotation\Attributes({ "id":"form-permanentDate", "class":"form-permanentDate form-control","placeholder":"" })
     */
    public $permanentDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Employee Status","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-employeeStatus", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-employeeStatus form-control","placeholder":"" })
     */
    public $employeeStatus;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Sift Applicable?"})
     * @Annotation\Attributes({ "id":"form-shiftApplicable", "class":"form-shiftApplicable form-control","placeholder":"" })
     */
    public $shiftApplicable;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Recommender","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-reccomender", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-reccomender form-control","placeholder":"" })
     */
    public $reccomender;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Approver","value_options":{"A":"A","B":"B","C":"C"}})
     * @Annotation\Attributes({ "id":"form-approver", "data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-approver form-control","placeholder":"" })
     */
    public $approver;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Basic Salary"})
     * @Annotation\Attributes({ "id":"form-basicSalary", "class":"form-basicSalary form-control","placeholder":"" })
     */
    public $basicSalary;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Include Payroll?"})
     * @Annotation\Attributes({ "id":"form-includePayroll", "class":"form-includePayroll form-control","placeholder":"" })
     */
    public $includePayroll;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contract Date"})
     * @Annotation\Attributes({ "id":"form-contractDate", "class":"form-contractDate form-control","placeholder":"" })
     */
    public $contractDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contract Period"})
     * @Annotation\Attributes({ "id":"form-contractPeriod", "class":"form-contractPeriod form-control","placeholder":"" })
     */
    public $contractPeriod;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"form-remarks", "class":"form-remarks form-control","placeholder":"" })
     */
    public $remarks;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Address"})
     * @Annotation\Attributes({ "id":"form-address", "class":"form-address form-control","placeholder":"" })
     */
    public $address;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"GO","class":"btn"})
    */
    public $submit;
}

