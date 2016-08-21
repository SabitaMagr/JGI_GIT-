<?php
namespace Setup\Form;

/**
* Form Setup Position
* Position Form.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 9, 2016, Wednesday 
* Last Modified By: 
* Last Modified Date: 
*/

use Zend\Form\Annotation;
use Setup\Model\ModelInterface;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Position")
*/

class PositionForm implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Position Id"})
	 * @Annotation\Attributes({ "id":"form-positionId", "class":"form-positionId form-control" })
	 */
	public $positionId;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Position Name"})
	 * @Annotation\Validator({"name":"StringLength", "options":{"min":"5"}})
	 * @Annotation\Attributes({ "id":"form-positionName", "class":"form-positionName form-control" })
	 */
	public $positionName;

	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Status","value_options":{"E":"Enabled","D":"Disabled"}})
     * @Annotation\Attributes({ "id":"form-status","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-status form-control"})
     */
    public $status;

	
	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;

    private $mappings=[
        'POSITION_ID'=>'positionId',
        'POSITION_NAME'=>'positionName',
        'REMARKS'=>'remarks',
        'STATUS'=>'status'
    ];

    public function exchangeArrayFromForm(array $data)
    {
        foreach($this->mappings as $key => $value){
            $this->{$value} = !empty($data[$value]) ? $data[$value] : null;
        }
    }

    public function exchangeArrayFromDB(array $data)
    {
        foreach($this->mappings as $key => $value){
            $this->{$value} = !empty($data[$key]) ? $data[$key] : null;
        }
    }
    public function getArrayCopyForDB()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$key]=$this->{$value};
        }
        return $tempArray;
    }

    public function getArrayCopyForForm()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$value]=$this->{$value};
        }
        return $tempArray;
    }
}

/* End of file PositionForm.php */
/* Location: ./Setup/src/Form/PositionForm.php */