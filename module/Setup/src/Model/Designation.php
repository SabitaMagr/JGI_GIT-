<?php

namespace Setup\Model;

use Zend\Form\Annotation;
use Zend\View\Model\ModelInterface;

class Designation extends Model
{
    public $designationId;

    public $designationCode;
    public $designationTitle;
    public $basicSalary;
    public $status;
    public $createdDt;
    public $modifiedDt;


    public $mappings =[
        'designationId'=>'DESIGNATION_ID',
        'designationCode'=>'DESIGNATION_CODE',
        'designationTitle'=>'DESIGNATION_TITLE',
        'basicSalary'=>'BASIC_SALARY',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];
}