<?php 
namespace Setup\Form;

use Zend\Form\Annotation;
use Setup\Model\Model;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Company")
*/

class CompanyForm{
	

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
	 * @Annotation\Options({"label":"Address"})
	 * @Annotation\Attributes({ "id":"form-address", "class":"form-address form-control" })
	 */
	public $address;


    /**
     * @Annotation\Type("Application\Custom\FormElement\Telephone")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone"})
     * @Annotation\Attributes({ "id":"form-telephone", "placeholder":"xx-xxxxxxx", "class":"form-control"})
     */
	public $telephone;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required(false)
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fax"})
	 * @Annotation\Attributes({ "id":"form-fax", "class":"form-fax form-control","placeholder":"Enter fax number.." })
	 */
	public $fax;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required(false)
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Swift"})
	 * @Annotation\Attributes({ "id":"form-swift", "class":"form-web form-control"})
	 */
	public $swift;

	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success pull-left"})
    */
    public $submit;


}
