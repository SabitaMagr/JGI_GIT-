<?php
namespace SelfService\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("TravelRequest")
 */

class TravelRequestForm{
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"form-employeeId","class":"form-control"})
     */
    public $employeeId;
    
}