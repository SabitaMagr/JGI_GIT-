<?php
namespace Loan\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("LoanRequest")
 */
 
class LoanClosing{

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Paid Amount"})
     * @Annotation\Attributes({ "id":"form-paidAmount","min":"0", "class":"form-requestedAmount form-control","step":"0.01" })
     */
    public $paymentAmount;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Paid Date"})
     * @Annotation\Attributes({ "id":"form-paidDate", "class":"form-loanDate form-control" })
     */
    public $paymentDate;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-reason","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}