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
     * @Annotation\Options({"label":"Salary"})
     * @Annotation\Attributes({ "id":"form-salaryRangeFrom", "class":"form-salaryRangeFrom form-control" })
     */
    public $salaryRangeFrom;    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary"})
     * @Annotation\Attributes({ "id":"form-salaryRangeTo", "class":"form-salaryRangeTo form-control" })
     */
    public $salaryRangeTo;   
     
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position List"})
     * @Annotation\Attributes({ "id":"positions","class":"form-control form-positions"})
     */
    public $positions;  
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designation List"})
     * @Annotation\Attributes({ "id":"designations","class":"form-control form-designations"})
     */
    public $designations;  
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Working Period"})
     * @Annotation\Attributes({ "id":"form-workingPeriodFrom", "class":"form-workingPeriodFrom form-control" })
     */
    public $workingPeriodFrom; 
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Working Period"})
     * @Annotation\Attributes({ "id":"form-workingPeriodTo", "class":"form-workingPeriodTo form-control" })
     */
    public $workingPeriodTo; 
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Type List"})
     * @Annotation\Attributes({ "id":"serviceTypes","class":"form-control form-serviceTypes"})
     */
    public $serviceType;     
}