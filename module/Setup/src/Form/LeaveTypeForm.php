<?php
namespace Setup\Form;
/**
* Form Setup Leave Type
* Leave Type Form.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 10, 2016, Wednesday 
* Last Modified By: 
* Last Modified Date: 
*/

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("LeaveType")
 */
class LeaveTypeForm{


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
	 * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
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
/* End of file LeaveTypeForm.php */
/* Location: ./Setup/src/Form/LeaveTypeForm.php */