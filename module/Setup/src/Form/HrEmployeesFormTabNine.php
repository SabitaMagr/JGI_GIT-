<?php
namespace Setup\Form;


use Application\Model\Model;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("HrEmployeesFormTabSeven")
 */

class HrEmployeesFormTabSeven extends Model{
    
    public $eRId;
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Relation"})
     * @Annotation\Attributes({ "id":"relationId","class":"form-control"})
     */
    
    public $relationId;
    
    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Organization Name"})
     * @Annotation\Attributes({ "id":"organizationName", "class":"form-control" })
     */
    
    public $personName;

    
     /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Date Of Birth"})
     * @Annotation\Attributes({"class":"form-control","id":"dob" })
     */
    public $dob;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Relation"})
     * @Annotation\Attributes({ "id":"relationId","class":"form-control"})
     */
    
    public $isNominee;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Relation"})
     * @Annotation\Attributes({ "id":"relationId","class":"form-control"})
     */
    public $isDependent;
    
     
    
    public $id;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approvedBy;
    public $approvedDate;
    public $status;
    
    
    public $mappings=[
        'id'=>'ID',
        'employeeId'=>'EMPLOYEE_ID',
        'organizationName'=>'ORGANIZATION_NAME',
        'organizationType'=>'ORGANIZATION_TYPE',
        'position'=>'POSITION',
        'fromDate'=>'FROM_DATE',
        'toDate'=>'TO_DATE',
        'remarks'=>'REMARKS',
        'companyId'=>'COMPANY_ID',
        'branchId'=>'BRANCH_ID',
        'createdBy'=>'CREATED_BY',
        'createdDate'=>'CREATED_DATE',
        'modifiedBy'=>'MODIFIED_BY',
        'modifiedDate'=>'MODIFIED_DATE',
        'approved'=>'APPROVED',
        'approvedBy'=>'APPROVED_BY',
        'approvedDate'=>'APPROVED_DATE',
        'status'=>'STATUS'
        
    ];
    
    
    

}

