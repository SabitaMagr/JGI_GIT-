<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("EmployeeType")
*/

class EmployeeType implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Employee Type Code"})
	 * @Annotation\Attributes({ "id":"form-employeeTypeCode", "class":"form-employeeTypeCode form-control" })
	 */
	public $employeeTypeCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Employee Type Name"})
	 * @Annotation\Attributes({ "id":"form-employeeTypeName", "class":"form-employeeTypeName form-control" })
	 */
	public $employeeTypeName;


	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control"})
     */
    public $remarks;
	
	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

    public function exchangeArray(array $data){
    	$this->employeeTypeCode = !empty($data['employeeTypeCode']) ? $data['employeeTypeCode'] : null;
    	$this->employeeTypeName = !empty($data['employeeTypeName']) ? $data['employeeTypeName'] : null;
    	$this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
    }

    public function getArrayCopy(){
    	return [
    		'employeeTypeCode' => $employeeTypeCode,
    		'employeeTypeName' => $employeeTypeName,
    		'remarks'=>$remarks

    	];
    }

}