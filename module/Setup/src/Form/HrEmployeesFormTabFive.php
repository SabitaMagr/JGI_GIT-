<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/31/16
 * Time: 11:52 AM
 */

namespace Setup\Form;

use Application\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabFive extends Model
{
    public $fileCode;

    public $employeeId;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"File Type"})
     * @Annotation\Attributes({ "id":"fileType","class":"full-width select2-offscreen","data-init-plugin":"select2"})
     */
    public $fileTypeCode;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"File Path"})
     * @Annotation\Attributes({ "id":"filePath", "class":"form-control" })
     * @Annotation\Input("Zend\InputFilter\FileInput")
     * @Annotation\Validator({"name":"Zend\Validator\File\Extension", "options":{"extension":{"jpg", "jpeg","png","gif"}}})
     */
    public $filePath;


    public $status;
    public $createdDt;
    public $modifiedDt;
    public $remarks;

    public $mappings=[
        'fileCode'=>'FILE_CODE',
        'employeeId'=>'EMPLOYEE_ID',
        'fileTypeCode'=>'FILETYPE_CODE',
        'filePath'=>'FILE_PATH',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT',
        'remarks'=>'REMARKS'
    ];

}