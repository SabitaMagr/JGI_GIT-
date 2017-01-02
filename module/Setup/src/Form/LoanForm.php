<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Loan")
 */
class LoanForm
{

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Loan Code"})
     * @Annotation\Attributes({ "id":"form-loanCode", "class":"form-loanCode form-control" })
     */
    public $loanCode;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Loan Name"})
     * @Annotation\Attributes({ "id":"form-loanName", "class":"form-loanName form-control" })
     */
    public $loanName;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Min. Amount"})
     * @Annotation\Attributes({ "id":"form-minAmount","class":"form-control"})
     */
    public $minAmount;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Max. Amount"})
     * @Annotation\Attributes({ "id":"form-maxAmount","class":"form-control"})
     */
    public $maxAmount;

     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Interest Rate"})
     * @Annotation\Attributes({ "id":"form-interestRate", "class":"form-interestRate form-control" })
     */
    public $interestRate;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Re-Payment Amount"})
     * @Annotation\Attributes({ "id":"form-repaymentAmount", "class":"form-repaymentAmount form-control" })
     */
    public $repaymentAmount;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Re-Payment Period"})
     * @Annotation\Attributes({ "id":"form-repaymentPeriod", "class":"form-repaymentPeriod form-control" })
     */
    public $repaymentPeriod;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Radio")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Deduct On Salary"})
     * @Annotation\Attributes({ "id":"form-deductOnSalary", "class":"form-deductOnSalary form-control" })
     */
    public $deductOnSalary;

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


}

