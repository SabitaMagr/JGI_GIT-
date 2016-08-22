<?php

namespace Setup\Model;

use Zend\Form\Annotation;
use Zend\View\Model\ModelInterface;

//use Zend\Form\Element\Textarea

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Designation")
 */
class Designation implements \Setup\Model\Model
{
    public $designationCode;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Designation Title:", "label_attributes":{"class":"sr-only"}})
     */
    public $designationTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Designation Detail:", "label_attributes":{"class":"sr-only"}})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Attribute({"style":"height: 50px; font-size:12px"})
     */
    public $designationDetail;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     */
    public $submit;

    public function exchangeArray(array $data)
    {
        $this->designationTitle = !empty($data['designationTitle']) ? $data['designationTitle'] : null;
        $this->designationDetail = !empty($data['designationDetail']) ? $data['designationDetail'] : null;

    }

    public function getArrayCopy()
    {
        return ['designationTitle' => $this->designationTitle, 'designationDetail' => $this->designationDetail];
    }


}