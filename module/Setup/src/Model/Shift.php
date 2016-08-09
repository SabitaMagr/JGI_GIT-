<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Shift")
*/

class Shift implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Shift Code"})
	 * @Annotation\Attributes({ "id":"form-shiftCode", "class":"form-shiftCode form-control" })
	 */
	public $shiftCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Shift Name"})
	 * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
	 * @Annotation\Attributes({ "id":"form-shiftName", "class":"form-shiftName form-control" })
	 */
	public $shiftName;


	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Start Time"})
	 * @Annotation\Attributes({ "id":"form-startTime", "class":"form-startTime form-control" })
	 */
	public $startTime;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"End Time"})
	 * @Annotation\Attributes({ "id":"form-endTime", "class":"form-endTime form-control" })
	 */
	public $endTime;

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
    	$this->shiftCode = !empty($data['shiftCode']) ? $data['shiftCode'] : null;
    	$this->shiftName = !empty($data['shiftName']) ? $data['shiftName'] : null;
    	$this->startTime = !empty($data['startTime']) ? $data['startTime'] : null;
    	$this->endTime = !empty($data['endTime']) ? $data['endTime'] : null;
    	$this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
    	$this->status = !empty($data['status']) ? $data['status'] : null;

    }

    public function exchangeArrayFromDb(array $data){
    	$this->shiftCode = !empty($data['SHIFT_CODE']) ? $data['SHIFT_CODE'] : null;
    	$this->shiftName = !empty($data['SHIFT_NAME']) ? $data['SHIFT_NAME'] : null;
    	$this->startTime = !empty($data['START_TIME']) ? $data['START_TIME'] : null;
    	$this->endTime = !empty($data['END_TIME']) ? $data['END_TIME'] : null;
    	$this->remarks = !empty($data['REMARKS']) ? $data['REMARKS'] : null;
    	$this->status = !empty($data['STATUS']) ? $data['STATUS'] : null;
    }

    public function getArrayCopyForDb(){
    	return [
            'SHIFT_CODE' => $this->shiftCode,
            'SHIFT_NAME' => $this->shiftName,
            'START_TIME' => $this->startTime,
            'END_TIME' => $this->endTime,
            'REMARKS' => $this->remarks,
    		'STATUS' => $this->status
           ];
    }

    public function getArrayCopyForForm(){
    	return [
            'shiftCode' => $this->shiftCode,
            'shiftName' => $this->shiftName,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'remarks' => $this->remarks,
    		'status' => $this->status

           ];
    }

}

