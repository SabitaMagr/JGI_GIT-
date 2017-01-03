<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("LoanPermission")
 */
class LoanPermissionForm
{

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Training Code"})
     * @Annotation\Attributes({ "id":"form-trainingCode", "class":"form-trainingCode form-control" })
     */
    public $trainingCode;    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Training Name"})
     * @Annotation\Attributes({ "id":"form-trainingName", "class":"form-trainingName form-control" })
     */
    public $trainingName;  
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Training Type"})
     * @Annotation\Attributes({ "id":"trainingType","class":"form-control form-trainingType"})
     */
    public $trainingType;  
    
}