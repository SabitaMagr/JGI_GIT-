<?php
namespace AttendanceManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Shift")
 */
class ShiftForm
{
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Shift Code"})
     * @Annotation\Attributes({ "id":"shiftCode", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"2"}})
     */
    public $shiftCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Shift Ename"})
     * @Annotation\Attributes({ "id":"shiftEname", "class":"form-control" })
     */
    public $shiftEname;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Shift Lname"})
     * @Annotation\Attributes({ "id":"shiftLname", "class":"form-control" })
     */
    public $shiftLname;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Time"})
     * @Annotation\Attributes({ "id":"startTime", "class":"form-control" })
     */
    public $startTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Time"})
     * @Annotation\Attributes({ "id":"endTime", "class":"form-control" })
     */
    public $endTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Half Day End Time"})
     * @Annotation\Attributes({ "id":"halfDayEndTime", "class":"form-control" })
     */
    public $halfDayEndTime;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Half Time"})
     * @Annotation\Attributes({ "id":"halfTime", "class":"form-control" })
     */
    public $halfTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"E":"Enabled","D":"Disabled"},"label":"Late In"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"lateIn"})
     */
    public $lateIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"E":"Enabled","D":"Disabled"},"label":"Early Out"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"earlyOut"})
     */
    public $earlyOut;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"StartDate"})
     * @Annotation\Attributes({ "id":"startDate", "class":"form-control" })
     */
    public $startDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"endDate", "class":"form-control" })
     */
    public $endDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday One"})
     * @Annotation\Attributes({ "id":"weekDay1"})
     */
    public $weekday1;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Two"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay2", "class":"form-control"})
     */
    public $weekday2;


    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Three"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay3"})
     */
    public $weekday3;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Four"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay4"})
     */
    public $weekday4;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Five"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay5"})
     */
    public $weekday5;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Six"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay6"})
     */
    public $weekday6;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Seven"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay7"})
     */
    public $weekday7;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Current Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"currentShift"})
     */
    public $currentShift;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"E":"Enabled","D":"Disabled"},"label":"Two Day Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"twoDayShift"})
     */
    public $twoDayShift;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Default Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"defaultShift"})
     */
    public $defaultShift;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-left"})
     */
    public $submit;

}
