<?php

namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("ServiceType")
 */
class ServiceTypeForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Service Type Name"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-serviceTypeName", "class":"form-serviceTypeName form-control" })
     */
    public $serviceTypeName;

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
     * @Annotation\Attributes({ "id":"form-status","class":"form-control"})
     */
    public $status;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}

/* End of file ServiceTypeForm.php */
/* Location: ./Setup/src/Form/ServiceTypeForm.php */