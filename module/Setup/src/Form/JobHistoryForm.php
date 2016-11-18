<?php
namespace Setup\Form;

/**
 * Form Setup Job History
 * Job History Form.
 * Created By: Somkala Pachhai
 * Edited By:
 * Date: August 11, 2016, Thursday
 * Last Modified By:
 * Last Modified Date:
 */

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("JobHistoryForm")
 */
class JobHistoryForm
{

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name","value_options":{"1":"Emp1","2":"Emp2"}})
     * @Annotation\Attributes({ "id":"employeeID","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Start Date"})
     * @Annotation\Attributes({"id":"startDate","class":"form-control"})
     */
    public $startDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({"id":"endDate", "class":"form-control"})
     */
    public $endDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Event Type Name"})
     * @Annotation\Attributes({ "id":"serviceEventTypeId","class":"form-control"})
     */
    public $serviceEventTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Service Type Name"})
     * @Annotation\Attributes({ "id":"fromServiceTypeId","class":"form-control"})
     */
    public $fromServiceTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Service Type Name"})
     * @Annotation\Attributes({ "id":"toServiceTypeId","class":"form-control"})
     */
    public $toServiceTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Branch Name"})
     * @Annotation\Attributes({ "id":"fromBranchId","class":"form-control"})
     */
    public $fromBranchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Branch Name"})
     * @Annotation\Attributes({ "id":"toBranchId","class":"form-control"})
     */
    public $toBranchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Department Name"})
     * @Annotation\Attributes({ "id":"fromDepartmentId","class":"form-control"})
     */
    public $fromDepartmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Department Name"})
     * @Annotation\Attributes({ "id":"toDepartmentId","class":"form-control"})
     */
    public $toDepartmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Designation Name"})
     * @Annotation\Attributes({ "id":"fromDesignationId","class":"form-control"})
     */
    public $fromDesignationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Designation Name"})
     * @Annotation\Attributes({ "id":"toDesignationId","class":"form-control"})
     */
    public $toDesignationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Position Name"})
     * @Annotation\Attributes({ "id":"fromPositionId","class":"form-control"})
     */
    public $fromPositionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Position Name"})
     * @Annotation\Attributes({ "id":"toPositionId","class":"form-control"})
     */
    public $toPositionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}

/* End of file JobHistoryForm.php */
/* Location: ./Setup/src/Form/JobHistoryForm.php */