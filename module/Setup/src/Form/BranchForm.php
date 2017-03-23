<?php

namespace Setup\Form;

/**
 * Form Setup Branch
 * Branch Form.
 * Created By: Ukesh Gaiju
 * Edited By: Somkala Pachhai
 * Date: August 3, 2016, Wednesday 
 * Last Modified By: Somkala Pachhai
 * Last Modified Date: August 10,2016, Wednesday 
 */
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Branch")
 */
class BranchForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Branch Code"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"form-branchCode", "class":"form-branchCode form-control" })
     */
    public $branchCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Branch Name"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"5","max":"255"}})
     * @Annotation\Attributes({ "id":"form-branchName", "class":"form-branchName form-control" })
     */
    public $branchName;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Street Address"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-streetAddress", "class":"form-streetAddress form-control"  })
     */
    public $streetAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Country"})
     * @Annotation\Attributes({ "id":"countryId","class":"form-control"})
     */
    public $countryId;
    
        /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;

    /**
     * @Annotation\Type("Application\Custom\FormElement\Telephone")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone"})
     * @Annotation\Attributes({ "id":"form-telephone", "placeholder":"xx-xxxxxxx", "class":"form-control"})
     */
    public $telephone;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"10"}})
     * @Annotation\Options({"label":"Fax"})
     * @Annotation\Attributes({ "id":"fax", "class":"form-control"})
     */
    public $fax;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email"})
     * @Annotation\Attributes({ "id":"email", "class":"form-control" })
     */
    public $email;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"form-control"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}

/* End of file BranchForm.php */
/* Location: ./Setup/src/Form/BranchForm.php */