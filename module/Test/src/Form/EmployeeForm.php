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
     * @Annotation\Attributes({ "id":"form-title", "class":"form-title form-control"})
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
     * @Annotation\Attributes({"id":"form-dateOfBirth", "class":"form-dateOfBirth form-control","placeholder":"Date of Birth..." })
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
     * @Annotation\Attributes({ "id":"form-gender", "class":"form-gender form-control"})
     */
    public $gender;
   
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

