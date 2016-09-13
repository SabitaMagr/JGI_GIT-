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

class HrEmployeesFormTabFour extends Model
{
    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Join Date"})
     * @Annotation\Attributes({ "id":"joinDate", "class":"form-control" })
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
     * @Annotation\Attributes({ "id":"salaryPf", "class":"form-control" })
     */
    public $salaryPf;

    public $mappings=[
        'joinDate'=>'JOIN_DATE',
        'salary'=>'SALARY',
        'salaryPf'=>'SALARY_PF',
    ];
}