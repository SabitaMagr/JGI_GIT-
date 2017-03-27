<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Advance")
 */
class AdvanceForm
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Code"})
     * @Annotation\Attributes({ "id":"form-advanceCode", "class":"form-advanceCode form-control" })
     */
    public $advanceCode;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Name"})
     * @Annotation\Attributes({ "id":"form-advanceName", "class":"form-advanceName form-control" })
     */
    public $advanceName;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Min. Salary Amount"})
     * @Annotation\Attributes({ "id":"form-minSalaryAmt","class":"form-control form-minSalaryAmt","step":"0.01"})
     */
    public $minSalaryAmt;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Max. Salary Amount"})
     * @Annotation\Attributes({ "id":"form-maxAmount","class":"form-control","step":"0.01"})
     */
    public $maxSalaryAmt;

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Amount To Allow(in %)"})
     * @Annotation\Attributes({ "id":"form-amountToAllow", "class":"form-amountToAllow form-control","step":"0.01" })
     */
    public $amountToAllow;
   
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Month To Allow(in month)"})
     * @Annotation\Attributes({ "id":"form-monthToAllow", "class":"form-monthToAllow form-control" })
     */
    public $monthToAllow;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;


}

