<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/2/16
 * Time: 5:39 PM
 */

namespace Payroll\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Monthly Value")
 */
class MonthlyValue
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"30"}})
     * @Annotation\Options({"label":"Monthly Value Code"})
     * @Annotation\Attributes({ "id":"mthCode","class":"form-control"})
     */
    public $mthCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Monthly Value EDesc"})
     * @Annotation\Attributes({ "id":"mthEdesc","class":"form-control"})
     */
    public $mthEdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Monthly Value NDesc"})
     * @Annotation\Attributes({ "id":"mthLdesc","class":"form-control"})
     */
    public $mthLdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Show At Rule"})
     * @Annotation\Attributes({ "id":"showAtRule","class":"form-control"})
     */
    public $showAtRule;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Sh Index No"})
     * @Annotation\Attributes({ "id":"shIndexNo","class":"form-control"})
     */
    public $shIndexNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
}