<?php
namespace Setup\Form;

/**
* Form Setup Department
* Department Form.
* Created By: Somkala Pachhai
* Edited By: Somkala Pachhai
* Date: August 5, 2016, Friday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10, 2016, Wednesday 
*/

use Zend\Form\Annotation;
use Setup\Model\Model;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Department")
*/
class DepartmentForm extends Model

{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Department Id"})
     * @Annotation\Attributes({ "id":"form-departmentId", "class":"form-departmentId form-control" })
     */
    public $departmentId;
	
    /**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Code"})
	 * @Annotation\Attributes({ "id":"form-departmentCode", "class":"form-departmentCode form-control" })
	 */
	public $departmentCode;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Name"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
	 * @Annotation\Attributes({ "id":"form-departmentName", "class":"form-departmentName form-control" })
	 */
	public $departmentName;

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
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Parent Department"})
     * @Annotation\Attributes({ "id":"form-parentDepartment","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-parentDepartment form-control"})
     */
	public $parentDepartment;

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

    private $mappings=[
        'DEPARTMENT_ID'=>'departmentId',
        'DEPARTMENT_CODE'=>'departmentCode',
        'DEPARTMENT_NAME'=>'departmentName',
        'PARENT_DEPARTMENT'=>'parentDepartment',
        'REMARKS'=>'remarks',
        'STATUS'=>'status'
    ];
}

/* End of file DepartmentForm.php */
/* Location: ./Setup/src/Form/DepartmentForm.php */