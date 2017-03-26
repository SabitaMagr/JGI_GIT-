<?php

namespace System\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("AttendanceDevice")
 */
class AttendanceDeviceForm {
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device name"})
     * @Annotation\Attributes({ "id":"deviceName", "class":" form-control" })
     */
    
    public $deviceName;
    
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device IP"})
     * @Annotation\Attributes({ "id":"deviceIp", "class":"form-control" })
     */
    public $deviceIp;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device Location"})
     * @Annotation\Attributes({ "id":"deviceLocation", "class":" form-control" })
     */
    
    public $deviceLocation;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch"})
     * @Annotation\Attributes({ "id":"branchId","class":"form-control"})
     */
    public $branchId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success","id":"btnSubmit"})
     */
    public $submit;
    
    
    
    
}
