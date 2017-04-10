<?php

namespace Setup\Form;

/**
* Master Setup for Designation
* Designation controller.
* Created By: Ukesh Gaiju
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Friday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Zend\Form\Annotation;
use Setup\Model\Model;


/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("DesignationForm")
 */

class DesignationForm
{
    

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Designation Code"})
     * @Annotation\Attributes({ "id":"form-designationCode", "class":"form-designationCode form-control" })
     */
    public $designationCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Designation Title"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
     * @Annotation\Attributes({ "id":"form-designationTitle", "class":"form-designationTitle form-control" })
     */
    public $designationTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Basic Salary"})
     * @Annotation\Attributes({ "id":"form-basicSalary" ,"min":"0","step":"0.01","class":"form-basicSalary form-control" })
     */
    public $basicSalary;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Parent Designation"})
     * @Annotation\Attributes({ "id":"form-parentDesignation","class":"form-control"})
     */
    public $parentDesignation;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Within Branch","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-withinBranch","class":"form-control"})
     */
    public $withinBranch;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Within Department","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-withinDepartment","class":"form-control"})
     */
    public $withinDepartment;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Status","value_options":{"E":"Enabled","D":"Disabled"}})
     * @Annotation\Attributes({ "id":"form-status","class":"form-control"})
     */
    public $status;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
    */
    public $submit;
    
        /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;

}

/* End of file DesignationForm.php */
/* Location: ./Setup/src/Form/DesignationForm.php */