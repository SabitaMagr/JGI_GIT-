<?php
namespace Setup\Form;


/**
* Form Setup Service Type
* Service Type Form.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 10, 2016, Wednesday 
* Last Modified By: 
* Last Modified Date: 
*/


use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("ServiceType")
*/

class ServiceTypeForm{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Service Type Code"})
	 * @Annotation\Attributes({ "id":"form-serviceTypeCode", "class":"form-serviceTypeCode form-control" })
	 */
	public $serviceTypeCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Service Type Name"})
	 * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
	 * @Annotation\Attributes({ "id":"form-serviceTypeName", "class":"form-serviceTypeName form-control" })
	 */
	public $serviceTypeName;


	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Status","value_options":{"E":"Enabled","D":"Disabled"}})
     * @Annotation\Attributes({ "id":"form-status","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-status form-control"})
     */
    public $status;
	

	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

}

/* End of file ServiceTypeForm.php */
/* Location: ./Setup/src/Form/ServiceTypeForm.php */