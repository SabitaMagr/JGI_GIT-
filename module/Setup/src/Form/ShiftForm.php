<?php
namespace Setup\Form;

/**
* Form Setup Shift
* Shift Form.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 10, 2016, Wednesday 
* Last Modified By: 
* Last Modified Date: 
*/

use Zend\Form\Annotation;
use Setup\Model\Model;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Shift")
*/

class ShiftForm extends Model{
	
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Shift Id"})
	 * @Annotation\Attributes({ "id":"form-shiftId", "class":"form-shiftId form-control" })
	 */
	public $shiftId;

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

    public $mappings = [
    	'SHIFT_ID'=>'shiftId',
    	'SHIFT_CODE'=>'shiftCode',
    	'SHIFT_NAME'=>'shiftName',
    	'START_TIME'=>'startTime',
    	'END_TIME'=>'endTime',
    	'REMARKS'=>'remarks',
    	'STATUS'=>'status'
    	];

}

/* End of file ShiftForm.php */
/* Location: ./Setup/src/Form/ShiftForm.php */