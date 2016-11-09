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
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Payroll\Model\Rules;


class Generate extends AbstractActionController
{

    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
        $employeeList=EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>'E'],' ');
        $rules=EntityHelper::getTableKVList($this->adapter,Rules::TABLE_NAME,Rules::PAY_ID,[Rules::PAY_EDESC],[Rules::STATUS=>'E'],null);
        return Helper::addFlashMessagesToArray($this, [
            'employeeList'=>$employeeList,
            'rules'=>$rules
        ]);
    }
}
