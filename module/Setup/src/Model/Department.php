<?php
namespace Setup\Model;



use Zend\Form\Annotation;

/** 
* @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
* @Annotation\Name("Department")
*/
class Department implements ModelInterface

{

	public $departmentId;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Code"})
	 * @Annotation\Attributes({ "id":"form-departmentCode", "class":"form-departmentCode form-control" })
	 */
	public $departmentCode;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Department Name"})
	 * @Annotation\Attributes({ "id":"form-departmentName", "class":"form-departmentName form-control" })
	 */
	public $departmentName;

	/**
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Remarks"})
	 * @Annotation\Attributes({ "id":"form-remarks", "class":"form-remarks form-control" })
	 */
	public $remarks;


	/**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Attributes({ "id":"form-parentDepartment","data-init-plugin":"cs-select","class":"cs-select cs-skin-slide form-parentDepartment form-control"})
     */
	public $parentDepartment;



	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Status"})
	 * @Annotation\Attributes({ "id":"form-status", "class":"form-status form-control" })
	 */
	public $status;

	public $createdDT;

	public $modifiedDT;

	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;


 


	public function exchangeArrayFromDB(array $data)
	{
        $this->departmentId = !empty($data['DEPARTMENT_ID']) ? $data['DEPARTMENT_ID'] : null;
        $this->departmentCode = !empty($data['DEPARTMENT_CODE']) ? $data['DEPARTMENT_CODE'] : null;
        $this->departmentName = !empty($data['DEPARTMENT_NAME']) ? $data['DEPARTMENT_NAME'] : null;
        $this->remarks = !empty($data['REMARKS']) ? $data['REMARKS'] : null;
        $this->parentDepartment = !empty($data['PARENT_DEPARTMENT']) ? $data['PARENT_DEPARTMENT'] : null;
        $this->status = !empty($data['STATUS']) ? $data['STATUS'] : null;
        $this->createdDT = !empty($data['CREATED_DT']) ? $data['CREATED_DT'] : null;
        $this->modifiedDT = !empty($data['MODIFIED_DT']) ? $data['MODIFIED_DT'] : null;
		return $this;
	}

	public function getArrayCopyForDB()
	{
        return [
            'DEPARTMENT_ID' => $this->departmentId,
            'DEPARTMENT_CODE' => $this->departmentCode,
            'DEPARTMENT_NAME' => $this->departmentName,
            'REMARKS' => $this->remarks,
            'PARENT_DEPARTMENT' => $this->parentDepartment,
            'STATUS' => $this->status,
            'CREATED_DT' => $this->createdDT,
            'MODIFIED_DT' => $this->modifiedDT,
           ];
	}

	public function getArrayCopyForForm()
	{
        return [
            'departmentId' => $this->departmentId,
            'departmentCode' => $this->departmentCode,
            'departmentName' => $this->departmentName,
            'remarks' => $this->remarks,
            'parentDepartment' => $this->parentDepartment,
            'status' => $this->status,
            'createdDT' => $this->createdDT,
            'modifiedDT' => $this->modifiedDT,
           ];
	}

	public function exchangeArrayFromForm(array $data)
	{
		$this->departmentId = !empty($data['departmentId']) ? $data['departmentId'] : null;
		$this->departmentCode = !empty($data['departmentCode']) ? $data['departmentCode'] : null;
		$this->departmentName = !empty($data['departmentName']) ? $data['departmentName'] : null;
		$this->remarks = !empty($data['remarks']) ? $data['remarks'] : null;
		$this->parentDepartment = !empty($data['parentDepartment']) ? $data['parentDepartment'] : null;
		$this->status = !empty($data['status']) ? $data['status'] : null;
		$this->createdDT = !empty($data['createdDT']) ? $data['createdDT'] : null;
		$this->modifiedDT = !empty($data['modifiedDT']) ? $data['modifiedDT'] : null;
		return $this;

	}
}
