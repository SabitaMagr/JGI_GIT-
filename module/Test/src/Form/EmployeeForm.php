<?php
namespace Test\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("User")
 */

class EmployeeForm
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Address:", "label_attributes":{"class":"sr-only"}})
     */
    public $address;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Sign In"})
     */
    public $submit;
}