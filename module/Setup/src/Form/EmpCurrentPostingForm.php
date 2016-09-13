<?php
namespace Setup\Form;

/**
* Form Setup Employee Current Posting
* Employee Current Posting Form.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 12, 2016, Friday 
* Last Modified By: 
* Last Modified Date: 
*/

use Application\Model\Model;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("JobHistoryForm")
*/

class EmpCurrentPostingForm extends Model{

	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"employeeId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Type Name"})
     * @Annotation\Attributes({ "id":"serviceTypeId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $serviceTypeId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch Name"})
     * @Annotation\Attributes({ "id":"branchId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $branchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Department Name"})
     * @Annotation\Attributes({ "id":"departmentId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $departmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designation Name"})
     * @Annotation\Attributes({ "id":"designationId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $designationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position Name"})
     * @Annotation\Attributes({ "id":"positionId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $positionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

    public $mappings = [
        'EMPLOYEE_ID'=>'employeeId',
        'SERVICE_TYPE_ID'=>'serviceTypeId',
        'BRANCH_ID'=>'branchId',
        'DEPARTMENT_ID'=>'departmentId',
        'DESIGNATION_ID'=>'designationId',
        'POSITION_ID'=>'positionId'
    ];

}
