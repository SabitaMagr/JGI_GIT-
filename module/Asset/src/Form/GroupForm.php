<?php

namespace Asset;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("AppraisalHeading")
 */

class GroupForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Asset Group Code"})
     * @Annotation\Attributes({"id":"assetGroupCode","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $assetGroupCode;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Asset Group NAME (in Eng.)"})
     * @Annotation\Attributes({"id":"assestGroupEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $assestGroupEdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Asset Group NAME (in Nep.)"})
     * @Annotation\Attributes({"id":"assetGroupNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    
    public $assetGroupNdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;

}
