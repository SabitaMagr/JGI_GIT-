<?php

namespace Payroll\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Rules")
 */
class Rules {

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Pay EDesc"})
     * @Annotation\Attributes({ "id":"payEdesc","class":"form-control"})
     */
    public $payEdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Pay LDesc"})
     * @Annotation\Attributes({ "id":"payLdesc","class":"form-control"})
     */
    public $payLdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"A":"Addition","D":"Deduction"},"label":"Pay Type"})
     * @Annotation\Attributes({ "id":"payTypeFlag","class":"form-control"})
     */
    public $payTypeFlag;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Priority Index"})
     * @Annotation\Attributes({ "id":"priorityIndex","class":"form-control"})
     */
    public $priorityIndex;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"label":"Is Monthly"})
     * @Annotation\Attributes({ "id":"isMonthly","class":"form-control"})
     */
    public $isMonthly;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Formula"})
     * @Annotation\Attributes({ "id":"formula","class":"form-control"})
     */
    public $formula;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks","class":"form-control"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
