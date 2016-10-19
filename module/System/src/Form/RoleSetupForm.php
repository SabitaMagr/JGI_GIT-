<?php
namespace System\Form;
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 3:11 PM
 */

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("RoleSetup")
 */

class RoleSetupForm{
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Role Name"})
     * @Annotation\Attributes({ "id":"form-roleName", "class":"form-roleName form-control" })
     */
    public $roleName;

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