<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/9/16
 * Time: 12:23 PM
 */

namespace AttendanceManagement\Controller;


use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class DailyAttendance extends AbstractActionController
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
        $employeeList = $this->pullEmployeeList();
        print "<pre>";
        foreach ($employeeList as $employee) {

        }
            exit;
        return [];
    }

    private function pullEmployeeList()
    {
        $employeeRepo = new EmployeeRepository($this->adapter);
        return $employeeRepo->fetchAll();
    }


}