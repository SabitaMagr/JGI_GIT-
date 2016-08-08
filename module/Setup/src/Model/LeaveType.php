<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("LeaveType")
 */
class LeaveType implements ModelInterface{


	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Leave Code"})
	 * @Annotation\Attributes({ "id":"form-leaveCode", "class":"form-leaveCode form-control" })
	 */
	public $leaveCode;

	
	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Leave Name"})
	 * @Annotation\Attributes({ "id":"form-leaveName", "class":"form-leaveName form-control" })
	 */
	public $leaveName;

	
	/**
	 * @Annotation\Type("Zend\Form\Element\Number")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Total Leave"})
	 * @Annotation\Attributes({ "id":"form-totalLeave", "class":"form-totalLeave form-control" })
	 */
	public $totalLeave;

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

    public function exchangeArray(array $data){
    	$this->leaveCode = !empty($data['leaveCode']) ? $data['leaveCode'] : null;
        $this->leaveName = !empty($data['leaveName']) ? $data['leaveName'] : null;
        $this->totalLeave = !empty($data['totalLeave']) ? $data['totalLeave'] : null;
        $this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
    }
    public function getArrayCopy(){
    	  return [
            'leaveCode' => $this->leaveCode,
            'leaveName' => $this->leaveName,
            'totalLeave' => $this->totalLeave,
            'remarks' => $this->remarks
           ];
    }



}
