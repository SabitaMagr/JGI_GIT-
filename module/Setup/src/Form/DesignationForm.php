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
     * @Annotation\Attributes({ "id":"form-designationTitle", "class":"form-designationTitle form-control" })
     */
    public $designationTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Basic Salary"})
     * @Annotation\Attributes({ "id":"form-basicSalary", "class":"form-basicSalary form-control" })
     */
    public $basicSalary;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
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

/* End of file DesignationForm.php */
/* Location: ./Setup/src/Form/DesignationForm.php */