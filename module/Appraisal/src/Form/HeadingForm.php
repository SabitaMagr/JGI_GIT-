<?php
namespace Appraisal\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("AppraisalHeading")
 */

class HeadingForm{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Heading Code"})
     * @Annotation\Attributes({"id":"headingCode","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $headingCode;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Heading Edesc"})
     * @Annotation\Attributes({"id":"headingEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     */
    public $headingEdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Heading Ndesc"})
     * @Annotation\Attributes({"id":"headingNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"400"}})
     */
    public $headingNdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Percentage"})
     * @Annotation\Attributes({"id":"percentage","class":"form-control","step":"0.01"})
     */
    public $percentage;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Appraisal Type"})
     * @Annotation\Attributes({"id":"appraisalTypeId","class":"form-control"})
     */
    public $appraisalTypeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;            
}