<?php
namespace LeaveManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("LeaveMaster")
 */
class LeaveMasterForm
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Leave Code"})
     * @Annotation\Attributes({ "id":"leaveCode", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"20"}})
     */
    public $leaveCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"leave Ename"})
     * @Annotation\Attributes({ "id":"leaveEname", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $leaveEname;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Leave Lname"})
     * @Annotation\Attributes({ "id":"leaveLname", "class":"form-control" })
     */
    public $leaveLname;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow Halfday"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"allowHalfday"})
     */
    public $allowHalfday;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Default Days"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"defaultDays"})
     */
    public $defaultDays;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Fiscal Year"})
     * @Annotation\Attributes({ "id":"fiscalYear","class":"form-control"})
     */
    public $fiscalYear;


    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Carry Forward"})
     * @Annotation\Attributes({ "id":"carryForward"})
     */
    public $carryForward;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Cashable"})
     * @Annotation\Attributes({ "id":"cashable"})
     */
    public $cashable;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
}