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
     * @Annotation\Attributes({ "id":"form-lastName", "class":"form-lastName form-control", "placeholder":"Last Name..."  })
     */
    public $lastName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"In Nepali"})
     * @Annotation\Attributes({ "id":"form-inNepali",  "class":"form-inNepali form-control","placeholder":"Name in Nepali..." })
     */
    public $inNepali;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Date of Birth"})
     * @Annotation\Attributes({"id":"start-date", "class":"form-dateOfBirth form-control","placeholder":"Date of Birth..." })
     */
    public $dateOfBirth;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Place of Birth"})
     * @Annotation\Attributes({ "id":"form-placeOfBirth","class":"form-placeOfBirth form-control", "placeholder":"Place of Birth..."})
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
     * @Annotation\Attributes({ "id":"form-nationalityAtBirth","class":"form-nationalityAtBirth form-control", "placeholder":"Nationality at Birth..."})
    */
    public $nationalityAtBirth;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Present Nationality"})
     * @Annotation\Attributes({ "id":"form-presentNationality","class":"form-presentNationality form-control", "placeholder":"Present Nationality..."})
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
     * @Annotation\Attributes({ "id":"form-socialActivities", "class":"form-socialActivities form-control","placeholder":"Social Activities..." })
     */
    public $socialActivities;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Extension No."})
     * @Annotation\Attributes({"id":"form-extensionNo","class":"form-extensionNo form-control","placeholder":"Extension No..."})
     */
    public $extensionNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Phone No."})
     * @Annotation\Attributes({ "id":"form-phoneNo", "class":"form-phoneNo form-control","placeholder":"Phone No..." })
     */
    public $phoneNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mobile No."})
     * @Annotation\Attributes({ "id":"form-mobileNo", "class":"form-mobileNo form-control","placeholder":"Mobile No..." })
     */
    public $mobileNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Official Email"})
     * @Annotation\Attributes({ "id":"form-officialEmail", "class":"form-officialEmail form-control","placeholder":"Official Email..." })
     */
    public $officialEmail;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Personal Email"})
     * @Annotation\Attributes({ "id":"form-personalEmail", "class":"form-personalEmail form-control","placeholder":"personal email..." })
     */
    public $personalEmail;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Social Network"})
     * @Annotation\Attributes({ "id":"form-socialNetwork", "class":"form-socialNetwork form-control","placeholder":"Social Network..." })
     */
    public $socialNetwork;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Highest Qualification"})
     * @Annotation\Attributes({ "id":"form-highestQualification", "class":"form-highestQualification form-control","placeholder":"highestQualification..." })
     */
    public $highestQualification;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Name"})
     * @Annotation\Attributes({ "id":"form-contactName", "class":"form-contactName form-control","placeholder":"Contact Name..." })
     */
    public $contactName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Relationship"})
     * @Annotation\Attributes({ "id":"form-relationship", "class":"form-relationship form-control","placeholder":"Relationship..." })
     */
    public $relationship;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Address"})
     * @Annotation\Attributes({ "id":"form-contactAddress", "class":"form-contactAddress form-control","placeholder":"Contact Address..." })
     */
    public $contactAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Contact Number"})
     * @Annotation\Attributes({ "id":"form-contactNumber", "class":"form-contactNumber form-control","placeholder":"Contact No..." })
     */
    public $contactNumber;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"House No."})
     * @Annotation\Attributes({ "id":"form-pHouseNo", "class":"form-pHouseNo form-control","placeholder":"House No..." })
     */
    public $pHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"House No."})
     * @Annotation\Attributes({ "id":"form-cHouseNo", "class":"form-cHouseNo form-control","placeholder":"House No..." })
     */
    public $cHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Ward No."})
     * @Annotation\Attributes({ "id":"form-pWardNo", "class":"form-pWardNo form-control","placeholder":"Ward No..." })
     */
    public $pWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Ward No"})
     * @Annotation\Attributes({ "id":"form-cWardNo", "class":"form-cWardNo form-control","placeholder":"Ward No..." })
     */
    public $cWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Street Address"})
     * @Annotation\Attributes({ "id":"form-pStreetAddress", "class":"form-pStreetAddress form-control","placeholder":"Street Address..." })
     */
    public $pStreetAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Street Address"})
     * @Annotation\Attributes({ "id":"form-cStreetAddress", "class":"form-cStreetAddress form-control","placeholder":"Street Address..." })
     */
    public $cStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"VDC/City"})
     * @Annotation\Attributes({ "id":"form-pVdcCity", "class":"form-pVdcCity form-control","placeholder":"VDC/City..." })
     */
    public $pVdcCity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"VDC/City"})
     * @Annotation\Attributes({ "id":"form-cVdcCity", "class":"form-cVdcCity form-control","placeholder":"VDC/City..." })
     */
    public $cVdcCity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"District"})
     * @Annotation\Attributes({ "id":"form-pDistrict", "class":"form-pDistrict form-control","placeholder":"District..." })
     */
    public $pDistrict;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"District"})
     * @Annotation\Attributes({ "id":"form-cDistrict", "class":"form-cDistrict form-control","placeholder":"District..." })
     */
    public $cDistrict;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Father's Name"})
     * @Annotation\Attributes({ "id":"form-fathersName", "class":"form-fathersName form-control","placeholder":"Father's Name..." })
     */
    public $fathersName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Father Occupation"})
     * @Annotation\Attributes({ "id":"form-fathersOccupation", "class":"form-fathersOccupation form-control","placeholder":"Father Occupation..." })
     */
    public $fathersOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mother's Name"})
     * @Annotation\Attributes({ "id":"form-mothersName", "class":"form-mothersName form-control","placeholder":"Mother's Name..." })
     */
    public $mothersName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Mother's Occupation"})
     * @Annotation\Attributes({ "id":"form-mothersOccupation", "class":"form-mothersOccupation form-control","placeholder":"Mother's Occupation..." })
     */
    public $mothersOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Grand Father Name"})
     * @Annotation\Attributes({ "id":"form-grandFatherName", "class":"form-grandFatherName form-control","placeholder":"Grand Father Name..." })
     */
    public $grandFatherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Grand Mother Name"})
     * @Annotation\Attributes({ "id":"form-grandMotherName", "class":"form-grandMotherName form-control","placeholder":"Grand Mother Name..." })
     */
    public $grandMotherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Address"})
     * @Annotation\Attributes({ "id":"form-address", "class":"form-address form-control","placeholder":"Address..." })
     */
    public $address;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"GO","class":"btn"})
    */
    public $submit;
}

