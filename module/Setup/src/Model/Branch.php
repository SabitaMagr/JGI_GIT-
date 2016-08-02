<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Branch")
 */

class Branch{

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Branch Code"})
	 * @Annotation\Attributes({ "id":"form-branchCode", "class":"form-branchCode form-control" })
	 */
	public $branchCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Branch Name"})
	 * @Annotation\Attributes({ "id":"form-branchName", "class":"form-branchName form-control" })
	 */
	public $branchName;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Location"})
	 * @Annotation\Attributes({ "id":"form-location", "class":"form-location form-control"  })
	 */
	public $location;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Telephone"})
	 * @Annotation\Attributes({ "id":"form-telephone", "class":"form-telephone form-control"})
	 */
	public $telephone;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fax"})
	 * @Annotation\Attributes({ "id":"form-fax", "class":"form-fax form-control"})
	 */
	public $fax;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Email"})
	 * @Annotation\Attributes({ "id":"form-email", "class":"form-email form-control"})
	 */
	public $email;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Contact Person"})
	 * @Annotation\Attributes({ "id":"form-contactPerson", "class":"form-contactPerson form-control"})
	 */
	public $contactPerson;

	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
      * @Annotation\Options({"label":"Branch Manager","value_options":{"A":"Anita","B":"Balaram","C":"Ceeta"}})
     * @Annotation\Attributes({ "id":"form-branchManager","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-branchManager form-control"})
     */
	public $branchManager;

	
	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Parent Branch","value_options":{"A":"Branch A","B":"Branch B","C":"Branch C"}})
     * @Annotation\Attributes({ "id":"form-parentBranch","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-parentBranch form-control"})
     */
	public $parentBranch;


	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

}