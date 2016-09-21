<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 4:18 PM
 */
namespace AttendanceManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Attendance By Hr")
     */
class AttendanceByHrForm
{
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee"})
     * @Annotation\Attributes({ "id":"employeeId","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Attendance Date"})
     * @Annotation\Attributes({ "id":"attendanceDt", "class":"form-control" })
     */
    public $attendanceDt;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"In Time"})
     * @Annotation\Attributes({ "id":"inTime", "class":"form-control" })
     */
    public $inTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Out Time"})
     * @Annotation\Attributes({ "id":"outTime", "class":"form-control" })
     */
    public $outTime;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"In Remarks"})
     * @Annotation\Attributes({ "id":"inRemarks", "class":"form-control" })
     */
    public $inRemarks;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Out Remarks"})
     * @Annotation\Attributes({ "id":"outRemarks", "class":"form-control" })
     */
    public $outRemarks;

    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Hour"})
     * @Annotation\Attributes({ "id":"totalHour", "class":"form-control" })
     */
    public $totalHour;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success pull-left"})
     */
    public $submit;
}