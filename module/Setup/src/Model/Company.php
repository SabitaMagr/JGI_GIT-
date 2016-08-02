<?php 
namespace Setup\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Employee")
*/

class Company{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Company Code"})
	 * @Annotation\Attributes({ "id":"form-companyCode", "class":"form-companyCode form-control", "placeholder":"Company Code..."  })
	 */
	public $companyCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Company Name"})
	 * @Annotation\Attributes({ "id":"form-companyName", "class":"form-companyName form-control" })
	 */
	public $companyName;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"In Nepali"})
	 * @Annotation\Attributes({ "id":"form-inNepali", "class":"form-inNepali form-control" })
	 */
	public $inNepali;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Address(I)"})
	 * @Annotation\Attributes({ "id":"form-adderessFirst", "class":"form-adderessFirst form-control" })
	 */
	public $addressFirst;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Address(II)"})
	 * @Annotation\Attributes({ "id":"form-addressSecond", "class":"form-addressSecond form-control" })
	 */
	public $addressSecond;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Telephone"})
	 * @Annotation\Attributes({ "id":"form-telephone", "class":"form-telephone form-control","placeholder":"Enter contact number.." })
	 */
	public $telephone;

	/**
	 * @Annotion\Type("Zend\Form\Element\Email")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Email"})
	 * @Annotation\Attributes({ "id":"form-email", "class":"form-email form-control","placeholder":"Enter email address.." })
	 */
	public $email;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fax"})
	 * @Annotation\Attributes({ "id":"form-fax", "class":"form-fax form-control","placeholder":"Enter fax number.." })
	 */
	public $fax;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Web"})
	 * @Annotation\Attributes({ "id":"form-web", "class":"form-web form-control","placeholder":"Enter website address..." })
	 */
	public $web;




}
