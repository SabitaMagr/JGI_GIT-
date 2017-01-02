<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Advance")
 */
class InstituteForm{
   /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Institute Code"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"form-instituteCode", "class":"form-instituteCode form-control" })
     */
    public $instituteCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Institute Name"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"5","max":"255"}})
     * @Annotation\Attributes({ "id":"form-instituteName", "class":"form-instituteName form-control" })
     */
    public $instituteName;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Location Detail"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-location", "class":"form-location form-control"  })
     */
    public $location;
    
    /**
     * @Annotation\Type("Application\Custom\FormElement\Telephone")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone"})
     * @Annotation\Attributes({ "id":"form-telephone", "placeholder":"xx-xxxxxxx", "class":"form-control"})
     */
    public $telephone;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email"})
     * @Annotation\Attributes({ "id":"email", "class":"form-control" })
     */
    public $email;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"form-control"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
 
}
