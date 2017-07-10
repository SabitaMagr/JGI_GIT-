<?php

namespace Setup\Form;

use Application\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabFour extends Model {

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Join Date"})
     * @Annotation\Attributes({"class":"form-control","id":"joinDate" })
     */
    public $joinDate;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"9"}})
     * @Annotation\Attributes({ "id":"salary", "class":"form-control" })
     */
    public $salary;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary PF"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"9"}})
     * @Annotation\Attributes({ "id":"salaryPf", "class":"form-control","min":"1" })
     */
    public $salaryPf;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Type Name"})
     * @Annotation\Attributes({ "id":"serviceTypeId","class":"form-control"})
     */
    public $appServiceTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Event Type Name"})
     * @Annotation\Attributes({ "id":"serviceEventTypeId","class":"form-control","value":"2","disabled":"disabled"})
     */
    public $appServiceEventTypeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position Name"})
     * @Annotation\Attributes({ "id":"positionId","class":"form-control"})
     */
    public $appPositionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designation Name"})
     * @Annotation\Attributes({ "id":"designationId","class":"form-control"})
     */
    public $appDesignationId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Department Name"})
     * @Annotation\Attributes({ "id":"departmentId","class":"form-control"})
     */
    public $appDepartmentId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch Name"})
     * @Annotation\Attributes({ "id":"branchId","class":"form-control"})
     */
    public $appBranchId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(False)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Type"})
     * @Annotation\Attributes({ "id":"employeeType","class":"form-control"})
     */
    
    public $employeeType;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"HR Flag","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-HRFlag","class":"form-control","value":"N"})
     */
    public $isHR;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"CEO Flag","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"form-CEOFlag","class":"form-control","value":"N"})
     */
    public $isCEO;
    
    public $branchId;
    public $departmentId;
    public $designationId;
    public $positionId;
    public $serviceTypeId;
    public $serviceEventTypeId;
    public $modifiedBy;
    public $modifiedDt;
    public $mappings = [
        'joinDate' => 'JOIN_DATE',
        'salary' => 'SALARY',
        'salaryPf' => 'SALARY_PF',
        'appServiceTypeId' => 'APP_SERVICE_TYPE_ID',
        'appServiceEventTypeId' => 'APP_SERVICE_EVENT_TYPE_ID',
        'appPositionId' => 'APP_POSITION_ID',
        'appDesignationId' => 'APP_DESIGNATION_ID',
        'appDepartmentId' => 'APP_DEPARTMENT_ID',
        'appBranchId' => 'APP_BRANCH_ID',
        'branchId' => 'BRANCH_ID',
        'departmentId' => 'DEPARTMENT_ID',
        'designationId' => 'DESIGNATION_ID',
        'positionId' => 'POSITION_ID',
        'serviceTypeId' => 'SERVICE_TYPE_ID',
        'serviceEventTypeId' => 'SERVICE_EVENT_TYPE_ID',
        'employeeType' => 'EMPLOYEE_TYPE',
        'modifiedBy' => 'MODIFIED_BY',
        'modifiedDt' => 'MODIFIED_DT',
        'isHR'=>'IS_HR',
        'isCEO'=>'IS_CEO'
    ];

}
