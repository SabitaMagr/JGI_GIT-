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
 * @Annotation\Name("UserSetup")
 */

class UserSetupForm{
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee"})
     * @Annotation\Attributes({ "id":"employeeID","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Role"})
     * @Annotation\Attributes({ "id":"roleId","class":"form-control"})
     */
    public $roleId;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Username"})
     * @Annotation\Attributes({ "id":"form-userName", "class":"form-userName form-control" })
     */
    public $userName;

    /**
     * @Annotion\Type("Zend\Form\Element\Password")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Password"})
     * @Annotation\Attributes({ "id":"form-password", "class":"form-password form-control" })
     */
    public $password;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Password")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Re-Enter Password"})
     * @Annotation\Attributes({ "id":"form-repassword", "class":"form-repassword form-control" })
     */
    public $repassword;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success","id":"btnSubmit"})
     */
    public $submit;
}