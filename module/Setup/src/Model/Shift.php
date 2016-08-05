<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Shift")
*/
class Shift implements ModelInterface

{

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Shift Code"})
	 * @Annotation\Attributes({"id":"form-shiftCode", "class":"form-shiftCode form-control" })
	*/
	public $shiftCode;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Shift Name"})
	 * @Annotation\Attributes({ "id":"form-shiftName", "class":"form-shiftName form-control" })
	 */
	public $shiftName;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Start Time"})
	 * @Annotation\Attributes({ "id":"form-startTime", "class":"form-startTime form-control" })
	 */
	public $startTime;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"End Time"})
	 * @Annotation\Attributes({ "id":"form-endTime", "class":"form-endTime form-control" })
	 */
	public $endTime;

	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

	    public function exchangeArray(array $data)
    {
        $this->shiftCode = !empty($data['shiftCode']) ? $data['shiftCode'] : null;
        $this->shiftName = !empty($data['shiftName']) ? $data['shiftName'] : null;
        $this->startTime = !empty($data['startTime']) ? $data['startTime'] : null;
        $this->endTime = !empty($data['endTime']) ? $data['endTime'] : null;
       
    }


	  public function getArrayCopy()
    {
        return [
            'shiftCode' => $this->shiftCode,
            'shiftName' => $this->shiftName,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime
           ];
           
    }

}

