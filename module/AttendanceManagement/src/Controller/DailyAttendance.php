<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/9/16
 * Time: 12:23 PM
 */

namespace AttendanceManagement\Controller;

use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use HolidayManagement\Repository\HolidayRepository;

class DailyAttendance extends AbstractActionController {

    private $adapter;
    private $date;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->date = Helper::getcurrentExpressionDate();
    }

    public function indexAction() {
        $employeeList = $this->pullEmployeeList();
        print "<pre>";
        foreach ($employeeList as $employee) {
            $checkForHoliday=$this->checkForHoliday($employee, $this->date);
            if($checkForHoliday==null){
                
            }else{
                
            }
        }
        exit;
        return [];
    }

    private function pullEmployeeList() {
        $employeeRepo = new EmployeeRepository($this->adapter);
        return $employeeRepo->fetchAll();
    }

    private function checkForHoliday(HrEmployees $employee, $date) {
        $holidayRepo = new HolidayRepository($this->adapter);
        return $holidayRepo->checkEmployeeOnHoliday($date, $employee->branchId, $employee->genderId);
    }

}
