<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/8/16
 * Time: 2:59 PM
 */

namespace Payroll\Controller;

use Application\Helper\DateHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceByHrRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;

class VariableProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;

    public function __construct($adapter, $employeeId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
    }

    public function processVariable($variable) {
        $processedValue = "";
        switch ($variable) {
            case PayrollGenerator::VARIABLES[0]:
                $processedValue = $this->employeeRepo->fetchById($this->employeeId)[HrEmployees::SALARY];
                $processedValue = ($processedValue == null) ? 0 : $processedValue;
                break;
            case PayrollGenerator::VARIABLES[1]:
                $currentMonth = date('m');
                $firstLastDate = DateHelper::getMonthFirstLastDate($currentMonth);
                $attendanceDetail = new AttendanceByHrRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate['firstDay']);
                $lastDayExp = Helper::getExpressionDate($firstLastDate['lastDay']);

                $days = $attendanceDetail->getNoOfDaysInDayInterval($this->employeeId, $firstDayExp, $lastDayExp);

//                $processedValue = cal_days_in_month(CAL_GREGORIAN, (int) date('n'), (int) date('Y'));
                $processedValue = $days;

                break;
            case PayrollGenerator::VARIABLES[2]:
                $currentMonth = date('m');
                $firstLastDate = DateHelper::getMonthFirstLastDate($currentMonth);
                $attendanceDetail = new AttendanceByHrRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate['firstDay']);
                $lastDayExp = Helper::getExpressionDate($firstLastDate['lastDay']);

                $days = $attendanceDetail->getNoOfDaysAbsent($this->employeeId, $firstDayExp, $lastDayExp);
                $processedValue = $days;
                break;
            case PayrollGenerator::VARIABLES[3]:
                $currentMonth = date('m');
                $firstLastDate = DateHelper::getMonthFirstLastDate($currentMonth);
                $attendanceDetail = new AttendanceByHrRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate['firstDay']);
                $lastDayExp = Helper::getExpressionDate($firstLastDate['lastDay']);

                $days = $attendanceDetail->getNoOfDaysPresent($this->employeeId, $firstDayExp, $lastDayExp);
                $processedValue = $days;

                break;
            case PayrollGenerator::VARIABLES[4]:


                break;
            case PayrollGenerator::VARIABLES[5]:


                break;
            case PayrollGenerator::VARIABLES[6]:


                break;
            case PayrollGenerator::VARIABLES[7]:


                break;
            case PayrollGenerator::VARIABLES[8]:


                break;
            case PayrollGenerator::VARIABLES[9]:


                break;
            case PayrollGenerator::VARIABLES[10]:


                break;
            default:


                break;
        }

        return $processedValue;
    }

}
