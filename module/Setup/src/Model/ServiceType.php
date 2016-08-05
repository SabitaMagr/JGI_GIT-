<?php
namespace Setup\Model;

use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("ServiceType")
*/

class ServiceType implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Service Type Code"})
	 * @Annotation\Attributes({ "id":"form-serviceTypeCode", "class":"form-serviceTypeCode form-control" })
	 */
	public $serviceTypeCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Service Type Name"})
	 * @Annotation\Attributes({ "id":"form-serviceTypeName", "class":"form-serviceTypeName form-control" })
	 */
	public $serviceTypeName;


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


    //to exchange array of data of form submit
    public function exchangeArray(array $data)
    {
        $this->serviceTypeCode = !empty($data['serviceTypeCode']) ? $data['serviceTypeCode'] : null;
        $this->serviceTypeName = !empty($data['serviceTypeName']) ? $data['serviceTypeName'] : null;
        $this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
        $this->status = !empty($data['status']) ? $data['status'] : null;
        
    }

    //to exchange array of data of database
    public function exchangeArrayDb(array $data)
    {
        $this->serviceTypeCode = !empty($data['SERVICE_TYPE_CODE']) ? $data['SERVICE_TYPE_CODE'] : null;
        $this->serviceTypeName = !empty($data['SERVICE_TYPE_NAME']) ? $data['SERVICE_TYPE_NAME'] : null;
        $this->remarks = !empty($data['REMARKS']) ? $data['REMARKS'] : null;
        $this->status = !empty($data['STATUS']) ? $data['STATUS'] : null;            
    }

    public function getArrayCopy()
    {
        return [
            'SERVICE_TYPE_CODE' => $this->serviceTypeCode,
            'SERVICE_TYPE_NAME' => $this->serviceTypeName,
            'REMARKS' => $this->remarks,
            'STATUS' => $this->status       
           ];
    }

    public function getLocalArrayCopy(){
        return [
            'serviceTypeCode' => $this->serviceTypeCode,
            'serviceTypeName' => $this->serviceTypeName,
            'remarks' => $this->remarks ,
            'status'=>$this->status       
           ];
    }




}