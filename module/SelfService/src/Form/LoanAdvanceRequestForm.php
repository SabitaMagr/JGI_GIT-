<?php
namespace SelfService\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("TrainingAssign")
 */

class LoanAdvanceRequestForm{
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"form-employeeId","class":"form-control"})
     */
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Loan/Advance Name"})
     * @Annotation\Attributes({ "id":"form-loanAdvanceId","class":"form-control"})
     */
    public $loanAdvanceId;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Request Amount"})
     * @Annotation\Attributes({ "id":"form-requestAmount", "class":"form-requestAmount form-control" })
     */
    public $requestAmount;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Repayment Period"})
     * @Annotation\Attributes({ "id":"form-repaymentPeriod", "class":"form-repaymentPeriod form-control" })
     */
    public $repaymentPeriod;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Amount"})
     * @Annotation\Attributes({ "id":"form-advanceAmount", "class":"form-advanceAmount form-control" })
     */
    public $advanceAmount;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Repayment Amount"})
     * @Annotation\Attributes({ "id":"form-repaymentAmount", "class":"form-repaymentAmount form-control" })
     */
    public $repaymentAmount;
    

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason"})
     * @Annotation\Attributes({"id":"form-reason","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    public $reason;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}