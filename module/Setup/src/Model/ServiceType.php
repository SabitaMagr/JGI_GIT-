<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("ServiceType")
*/

class ServiceType implements ModelInterface{
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
	 * @Annotation\Attributes({ "id":"form-serviceTypeName", "class":"form-serviceTypeName form-control" })
	 */
	public $serviceTypeName;


	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;
	
	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;



    public function exchangeArray(array $data)
    {
        $this->serviceTypeCode = !empty($data['serviceTypeCode']) ? $data['serviceTypeCode'] : null;
        $this->serviceTypeName = !empty($data['serviceTypeName']) ? $data['serviceTypeName'] : null;
        $this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
        
    }

    public function getArrayCopy()
    {
        return [
            'serviceTypeCode' => $this->serviceTypeCode,
            'serviceTypeName' => $this->serviceTypeName,
            'remarks' => $this->remarks        
           ];
    }




}