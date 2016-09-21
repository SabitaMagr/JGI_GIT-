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
     * @Annotation\Options({"label":"Employee Name","value_options":{"1":"Emp1","2":"Emp2"}})
     * @Annotation\Attributes({ "id":"employeeId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
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
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Type Name"})
     * @Annotation\Attributes({ "id":"serviceTypeId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $serviceTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Branch Name"})
     * @Annotation\Attributes({ "id":"fromBranchId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $fromBranchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Branch Name"})
     * @Annotation\Attributes({ "id":"toBranchId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $toBranchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Department Name"})
     * @Annotation\Attributes({ "id":"fromDepartmentId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $fromDepartmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Department Name"})
     * @Annotation\Attributes({ "id":"toDepartmentId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $toDepartmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Designation Name"})
     * @Annotation\Attributes({ "id":"form-fromDesignationId","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-fromDesignationId form-control"})
     * @Annotation\Attributes({ "id":"fromDesignationId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $fromDesignationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Designation Name"})
     * @Annotation\Attributes({ "id":"toDesignationId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $toDesignationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"From Position Name"})
     * @Annotation\Attributes({ "id":"fromPositionId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $fromPositionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"To Position Name"})
     * @Annotation\Attributes({ "id":"toPositionId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $toPositionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success pull-right"})
     */
    public $submit;

}

/* End of file JobHistoryForm.php */
/* Location: ./Setup/src/Form/JobHistoryForm.php */