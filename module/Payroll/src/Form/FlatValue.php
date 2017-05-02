<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/3/16
 * Time: 1:35 PM
 */

namespace Payroll\Form;

use Zend\Form\Annotation;


/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Flat Value")
 */
class FlatValue
{

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Flat EDesc"})
     * @Annotation\Attributes({ "id":"flatEdesc","class":"form-control"})
     */
    public $flatEdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     * @Annotation\Options({"label":"Flat LDesc"})
     * @Annotation\Attributes({ "id":"flatLdesc","class":"form-control"})
     */
    public $flatLdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Show At Rule"})
     * @Annotation\Attributes({ "id":"showAtRule","class":"form-control"})
     */
    public $showAtRule;

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
