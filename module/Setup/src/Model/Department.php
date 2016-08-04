<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Department")
*/
class Department implements ModelInterface

{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Code"})
	 * @Annotation\Attributes({ "id":"form-departmentCode", "class":"form-departmentCode form-control" })
	 */
	public $departmentCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Name"})
	 * @Annotation\Attributes({ "id":"form-departmentName", "class":"form-departmentName form-control" })
	 */
	public $departmentName;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"H.O.D code"})
	 * @Annotation\Attributes({ "id":"form-hodCode", "class":"form-hodCode form-control" })
	 */
	public $hodCode;

	/**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Attributes({ "id":"form-parentDepartment","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-parentDepartment form-control"})
     */
	public $parentDepartment;


	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;


 

    public function exchangeArray(array $data)
    {
        $this->departmentCode = !empty($data['departmentCode']) ? $data['departmentCode'] : null;
        $this->departmentName = !empty($data['departmentName']) ? $data['departmentName'] : null;
        $this->hodCode = !empty($data['hodCode']) ? $data['hodCode'] : null;
        $this->parentDepartment = !empty($data['parentDepartment']) ? $data['parentDepartment'] : null;
       
    }

    public function getArrayCopy()
    {
        return [
            'departmentCode' => $this->departmentCode,
            'departmentName' => $this->departmentName,
            'hodCode' => $this->hodCode,
            'parentDepartment' => $this->parentDepartment
           ];
           
    }

}