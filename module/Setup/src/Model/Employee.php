<?php
namespace Setup\Model;

use Zend\Form\Annotation;


/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Employee")
 */
class Employee
{

    public $employeeCode;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"First Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeFirstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeMiddleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"First Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeLastName;



    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Sign In"})
     */
    public $submit;

    public function exchangeArray(array $data){
        $this->employeeCode=!empty($data['employeeCode']) ? $data['employeeCode'] : null;
        $this->employeeFirstName=!empty($data['employeeFirstName'])?$data['employeeFirstName']:null;
        $this->employeeLastName=!empty($data['employeeLastName'])?$data['employeeLastName']:null;
        $this->employeeMiddleName=!empty($data['employeeMiddleName'])?$data['employeeMiddleName']:null;


    }

    public function getArrayCopy()
    {
        return [
//            'employeeCode'     => $this->employeeCode,
            'employeeFirstName' => $this->employeeFirstName,
            'employeeMiddleName'  => $this->employeeMiddleName,
            'employeeLastName'  => $this->employeeLastName,
        ];
    }

}