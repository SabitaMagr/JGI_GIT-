<?php
namespace Setup\Form;

use Zend\Form\Annotation;


/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("functionalLevels")
 */
class FunctionalLevelsForm {
    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelsCode", "class":"form-control" })
     */
    public $functionalLevelsCode;

    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level Edesc"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelsEdesc", "class":"form-control" })
     */
    public $functionalLevelsEdesc;
    
    
    
     /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level Ldesc"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelsLdesc", "class":"form-control" })
     */
    public $functionalLevelsLdesc;

    
    /**
     * @Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
