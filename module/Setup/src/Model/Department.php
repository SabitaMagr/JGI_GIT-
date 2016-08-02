<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Department")
*/

class Department{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Code"})
	 * @Annotation\Attributes({ "id":"form-departmentCode", "class":"form-departmentCode form-control" })
	 */
	public $departmentCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Name"})
	 * @Annotation\Attributes({ "id":"form-departmentName", "class":"form-departmentName form-control" })
	 */
	public $departmentName;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"H.O.D code"})
	 * @Annotation\Attributes({ "id":"form-hodCode", "class":"form-hodCode form-control" })
	 */
	public $hodCode;

	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Parent Department", "value_options":{"A":"ABC","D":"DEF","H":"HJK"}})
     * @Annotation\Attributes({ "id":"form-parentDepartment","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-parentDepartment form-control"})
     */
	public $parentDepartment;

	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

}