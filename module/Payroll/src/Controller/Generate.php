<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/20/16
 * Time: 4:32 PM
 */

namespace Payroll\Controller;


use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValue as FlatValueModel;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\RulesDetailRepo;
use PHPExcel;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;


class Generate extends AbstractActionController
{

    private $adapter;
    private $flatValueDetRepo;
    private $monthlyValueDetRepo;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
//        $employeeFormElement = new Select();
//        $employeeFormElement->setName('Employees');
//        $employeeList=EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>'E'],' ');
//
//        $employeeList[-1]="All";
//        ksort($employeeList);
//        $employeeFormElement->setValueOptions($employeeList);
//        $employeeFormElement->setAttributes(["id" => "employeeId", "class" => "form-control", "data-init-plugin" => "select2"]);
//        $employeeFormElement->setLabel("Employee");

        $employeeList=EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>'E'],' ');
        $rules=EntityHelper::getTableKVList($this->adapter,Rules::TABLE_NAME,Rules::PAY_ID,[Rules::PAY_EDESC],[Rules::STATUS=>'E'],null);
        return Helper::addFlashMessagesToArray($this, [
            'employeeList'=>$employeeList,
            'rules'=>$rules
        ]);
    }
}
