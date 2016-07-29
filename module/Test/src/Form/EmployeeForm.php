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
     * @Annotation\Filter({"name":"StripTags"})
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
     * @Annotation\Filter({"name":"StripTags"})
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
     * @Annotation\Filter({"name":"StripTags"})
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
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Social Activities"})
     * @Annotation\Attributes({ "id":"form-socialActivities", "class":"form-socialActivities form-control","placeholder":"Social Activities..." })
     */
    public $socialActivities;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
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

