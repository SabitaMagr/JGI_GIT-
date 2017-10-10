<?php

namespace Setup\Form;

use Zend\Form\Annotation\Type;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("DesignationForm")
 */
class DesignationForm {

    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Designation Title"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-designationTitle", "class":"form-designationTitle form-control" })
     */
    public $designationTitle;

    /**
     * @Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Basic Salary"})
     * @Annotation\Attributes({ "id":"form-basicSalary" ,"min":"0","step":"0.01","class":"form-basicSalary form-control" })
     */
    public $basicSalary;

    /**
     * @Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Parent Designation"})
     * @Annotation\Attributes({ "id":"form-parentDesignation","class":"form-control"})
     */
    public $parentDesignation;

    /**
     * @Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Within Branch","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-withinBranch","class":"form-control"})
     */
    public $withinBranch;

    /**
     * @Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Within Department","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-withinDepartment","class":"form-control"})
     */
    public $withinDepartment;

    /**
     * @Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Status","value_options":{"E":"Enabled","D":"Disabled"}})
     * @Annotation\Attributes({ "id":"form-status","class":"form-control"})
     */
    public $status;

    /**
     * @Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

    /**
     * @Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;

}

/* End of file DesignationForm.php */
/* Location: ./Setup/src/Form/DesignationForm.php */