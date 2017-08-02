<?php

namespace Payroll\Controller;

use Payroll\Repository\PayrollRepository;
use SelfService\Repository\AdvanceRequestRepository;
use Setup\Repository\EmployeeRepository;

class VariableProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $monthId;
    private $payrollRepo;

    public function __construct($adapter, $employeeId, int $monthId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->monthId = $monthId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $this->payrollRepo = new PayrollRepository($this->adapter);
    }

    public function processVariable($variable) {
        $processedValue = "";
        switch ($variable) {
            /*
             * BASIC_SALARY
             */
            case PayrollGenerator::VARIABLES[0]:
                $processedValue = $this->payrollRepo->fetchBasicSalary($this->employeeId);
                break;
            /*
             * NO_OF_WORKING_DAYS
             */
            case PayrollGenerator::VARIABLES[1]:
                $processedValue = $this->payrollRepo->getNoOfWorkingDays($this->employeeId, $this->monthId);
                break;
            /*
             * NO_OF_DAYS_ABSENT
             */
            case PayrollGenerator::VARIABLES[2]:
                $processedValue = $this->payrollRepo->getNoOfDaysAbsent($this->employeeId, $this->monthId);
                break;
            /*
             * NO_OF_DAYS_WORKED
             */
            case PayrollGenerator::VARIABLES[3]:
                $processedValue = $this->payrollRepo->getNoOfDaysPresent($this->employeeId, $this->monthId);
                break;
            /*
             * NO_OF_PAID_LEAVES
             */
            case PayrollGenerator::VARIABLES[4]:
                $processedValue = $this->payrollRepo->getNoOfPaidLeaves($this->employeeId, $this->monthId);
                break;
            /*
             * NO_OF_UNPAID_LEAVES
             */
            case PayrollGenerator::VARIABLES[5]:
                $processedValue = $this->payrollRepo->getNoOfUnpaidLeaves($this->employeeId, $this->monthId);
                break;
            /*
             * GENDER
             */
            case PayrollGenerator::VARIABLES[6]:
                $processedValue = $this->payrollRepo->getEmployeeGender($this->employeeId);
                break;
            /*
             * MARITUAL_STATUS
             */
            case PayrollGenerator::VARIABLES[7]:
                $processedValue = $this->payrollRepo->getEmployeeMaritualStatus($this->employeeId);
                break;
            /*
             * TOTAL_DAYS_FROM_JOIN_DATE
             */
            case PayrollGenerator::VARIABLES[8]:
                $processedValue = $this->payrollRepo->getEmployeeTotalDaysFromJoinDate($this->employeeId, $this->monthId);
                break;
            /*
             * SERVICE_TYPE
             */
            case PayrollGenerator::VARIABLES[9]:
                $processedValue = $this->payrollRepo->getEmployeeServiceType($this->employeeId);
                break;
            /*
             * NO_OF_WORKING_DAYS_INC_HOLIDAYS
             */
            case PayrollGenerator::VARIABLES[10]:
                $processedValue = $this->payrollRepo->getNoOfWorkingDaysIncDayOffAndHoliday($this->employeeId, $this->monthId);
                break;
            /*
             * TOTAL_NO_OF_WORK_DAYS_INC_HOLIDAYS
             */
            case PayrollGenerator::VARIABLES[11]:
                $processedValue = $this->payrollRepo->getNoOfDaysWorkedIncDayOffAndHoliday($this->employeeId, $this->monthId);
                break;
            /*
             * SALARY_REVIEW_DAY
             */
            case PayrollGenerator::VARIABLES[12]:
                $processedValue = $this->payrollRepo->getNoOfDaysWorkedIncDayOffAndHoliday($this->employeeId, $this->monthId);
                break;
            /*
             * SALARY_REVIEW_OLD_SALARY
             */
            case PayrollGenerator::VARIABLES[13]:
                $processedValue = $this->payrollRepo->getEmployeeSalaryReviewDay($this->employeeId, $this->monthId);
                break;
            /*
             * HAS_ADVANCE
             */
            case PayrollGenerator::VARIABLES[14]:
                $advanceRequestRepo = new AdvanceRequestRepository($this->adapter);
                $processedValue = $advanceRequestRepo->checkAdvance($this->employeeId, $this->monthId);
                break;
            /*
             * ADVANCE_AMT
             */
            case PayrollGenerator::VARIABLES[15]:
                $advanceRequestRepo = new AdvanceRequestRepository($this->adapter);
                $processedValue = $advanceRequestRepo->getAdvance($this->employeeId, $this->monthId);
                break;
            default:


                break;
        }

        return $processedValue;
    }

}
