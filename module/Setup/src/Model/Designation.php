<?php

namespace Setup\Model;

use Zend\Form\Annotation;

//use Zend\Form\Element\Textarea

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Designation")
 */
class Designation
{
    public $designationCode;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     */

    public $designationTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     */
    public $designationDetail;


}