<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Position")
*/

class Position implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Position Code"})
	 * @Annotation\Attributes({ "id":"form-positionCode", "class":"form-positionCode form-control" })
	 */
	public $positionCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Position Name"})
	 * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
	 * @Annotation\Attributes({ "id":"form-positionName", "class":"form-positionName form-control" })
	 */
	public $positionName;

	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
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

    public function exchangeArrayFromForm(array $data){
    	$this->positionCode = !empty($data['positionCode']) ? $data['positionCode'] : null;
    	$this->positionName = !empty($data['positionName']) ? $data['positionName'] : null;
    	$this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
    	$this->status = !empty($data['status']) ? $data['status'] : null;

    }
    public function getArrayCopyForForm(){
    	return [
    		'positionCode' => $this->positionCode,
    		'positionName'=>$this->positionName,
    		'remarks' => $this->remarks,
    		'status' => $this->status
    	];
    }

    public function exchangeArrayFromDB(array $data){
    	$this->positionCode = !empty($data['POSITION_CODE']) ? $data['POSITION_CODE'] : null;
    	$this->positionName = !empty($data['POSITION_NAME']) ? $data['POSITION_NAME'] : null;
    	$this->remarks = !empty($data['REMARKS']) ? $data['REMARKS'] : null;
    	$this->status = !empty($data['STATUS']) ? $data['STATUS'] : null;
    }

    public function getArrayCopyForDb(){
    	return [
    		'POSITION_CODE' => $this->positionCode,
    		'POSITION_NAME'=>$this->positionName,
    		'REMARKS' => $this->remarks,
    		'STATUS' => $this->status
    	];
    }

}