<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Training")
 */
class TrainingForm
{

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Training Code"})
     * @Annotation\Attributes({ "id":"form-trainingCode", "class":"form-trainingCode form-control" })
     */
    public $trainingCode;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Training Name"})
     * @Annotation\Attributes({ "id":"form-trainingName", "class":"form-trainingName form-control" })
     */
    public $trainingName;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Date"})
     * @Annotation\Attributes({ "id":"form-startDate", "class":"form-startDate form-control" })
     */
    public $startDate;
     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"form-endDate", "class":"form-endDate form-control" })
     */
    public $endDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;


}

