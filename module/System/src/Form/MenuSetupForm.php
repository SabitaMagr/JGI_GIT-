<?php
namespace System\Form;
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/18/16
 * Time: 1:56 PM
 */
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("MenuSetup")
 */
class MenuSetupForm{
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Menu Code"})
     * @Annotation\Attributes({ "id":"form-menuCode", "class":"form-menuCode form-control" })
     */
    public $menuCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Menu Name"})
     * @Annotation\Attributes({ "id":"form-menuName", "class":"form-menuName form-control" })
     */
    public $menuName;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Url"})
     * @Annotation\Attributes({ "id":"form-url", "class":"form-url form-control" })
     */
    public $url;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Icon Class"})
     * @Annotation\Attributes({ "id":"form-iconClass", "class":"form-iconClass form-control" })
     */
    public $iconClass;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Parent Menu"})
     * @Annotation\Attributes({ "id":"parentMenu","class":"form-control"})
     */
    public $parentMenu;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Menu Description"})
     * @Annotation\Attributes({"id":"form-menuDescription","class":"form-menuDescription form-control","style":"    height: 50px; font-size:12px"})
     */
    public $menuDescription;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
}
