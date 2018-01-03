<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("CustomerContract")
 * @Annotation\Attributes({"id":"customerContract"})
 */
class CustomerContractFrom {

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Customer"})
     * @Annotation\Attributes({ "id":"customerId","class":"form-control"})
     */
    public $customerId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"StartDate"})
     * @Annotation\Attributes({ "id":"startDate","class":"form-control" })
     */
    public $startDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"endDate","class":"form-control" })
     */
    public $endDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"In Time"})
     * @Annotation\Attributes({ "id":"inTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control" })
     */
    public $inTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Out Time"})
     * @Annotation\Attributes({ "id":"outTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $outTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Working Hour"})
     * @Annotation\Attributes({ "id":"workingHours", "data-format":"h:mm", "data-template":"hh : mm", "class":"form-control" })
     */
    public $workingHours;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"W":"Weekly","R":"Randomly"},"label":"Working Cycle"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"workingCycle"})
     */
    public $workingCycle;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"H":"Hourly","D":"Day wise","W":"Weekly","M":"Monthly"},"label":"Charge Type"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"chargeType"})
     */
    public $chargeType;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Charge Rate"})
     * @Annotation\Attributes({"id":"chargeRate","class":"form-control","min":"0","step":"0.01"})
     */
    public $chargeRate;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"512"}})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
