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

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("JobHistoryForm")
*/

class EmpCurrentPostingForm{

	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Employee Name","value_options":{"1":"Emp1","2":"Emp2"}})
     * @Annotation\Attributes({ "id":"form-employeeId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-employeeId form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Type Name"})
     * @Annotation\Attributes({ "id":"form-serviceTypeId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-serviceTypeId form-control"})
     */
    public $serviceTypeId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch Name"})
     * @Annotation\Attributes({ "id":"form-branchId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-branchId form-control"})
     */
    public $branchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Department Name"})
     * @Annotation\Attributes({ "id":"form-departmentId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-departmentId form-control"})
     */
    public $departmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designation Name"})
     * @Annotation\Attributes({ "id":"form-designationId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-designationId form-control"})
     */
    public $designationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position Name"})
     * @Annotation\Attributes({ "id":"form-positionId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-positionId form-control"})
     */
    public $positionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

}
/* End of file EmpCurrentPosting.php */
/* Location: ./Setup/src/Form/EmpCurrentPosting.php */