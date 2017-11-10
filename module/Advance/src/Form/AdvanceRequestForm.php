<?php

namespace Advance\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("AdvanceRequest")
 */
class AdvanceRequestForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Advance Name"})
     * @Annotation\Attributes({ "id":"advanceId","class":"form-control"})
     */
    public $advanceId;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Request Amount"})
     * @Annotation\Attributes({ "id":"requestedAmount", "class":" form-control","min":"1"})
     */
    public $requestedAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Date"})
     * @Annotation\Attributes({ "id":"dateOfadvance", "class":"form-control" })
     */
    public $dateOfadvance;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason"})
     * @Annotation\Attributes({"id":"reason","class":"form-control","style":"    height: 50px; font-size:12px"})
     */
    public $reason;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"recommendedBy","class":"form-control"})
     */
    public $recommendedBy;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason"})
     * @Annotation\Attributes({"id":"recommendedRemarks","class":"form-control","style":"    height: 50px; font-size:12px"})
     */
    public $recommendedRemarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"approvedBy","class":"form-control"})
     */
    public $approvedBy;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason"})
     * @Annotation\Attributes({"id":"approvedRemarks","class":"form-control","style":"    height: 50px; font-size:12px"})
     */
    public $approvedRemarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"S":"Salary","M":"Monthly"},"label":"Deduction Type"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"deductionType"})
     */
    public $deductionType;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Monthly Deduction Rate"})
     * @Annotation\Attributes({ "id":"deductionRate","class":"form-control","step":"0.01","min":"0","max":"100"})
     */
    public $deductionRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":" Max Deduction Month"})
     * @Annotation\Attributes({ "id":"deductionIn","class":"form-control","min":"0"})
     */
    public $deductionIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
