<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/8/16
 * Time: 2:59 PM
 */

namespace Payroll\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Months;
use Application\Repository\MonthRepository;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Repository\SalaryDetailRepo;
use Setup\Model\HrEmployees;
use Setup\Model\ServiceType;
use Setup\Repository\EmployeeRepository;

class VariableProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $monthId;

    public function __construct($adapter, $employeeId, int $monthId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->monthId = $monthId;
        $this->employeeRepo = new EmployeeRepository($adapter);
    }

    public function processVariable($variable) {
        $processedValue = "";
        switch ($variable) {
//            "BASIC_SALARY"
            case PayrollGenerator::VARIABLES[0]:
                $processedValue = $this->employeeRepo->fetchById($this->employeeId)[HrEmployees::SALARY];
                $processedValue = ($processedValue == null) ? 0 : $processedValue;
                break;
//            "NO_OF_WORKING_DAYS"
            case PayrollGenerator::VARIABLES[1]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);

                $days = $attendanceDetail->getNoOfDaysInDayInterval($this->employeeId, $firstDayExp, $lastDayExp);
                $processedValue = $days;
                break;
//            "NO_OF_DAYS_ABSENT"
            case PayrollGenerator::VARIABLES[2]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);

                $days = $attendanceDetail->getNoOfDaysAbsent($this->employeeId, $firstDayExp, $lastDayExp);
                $processedValue = $days;
                break;
//            "NO_OF_DAYS_WORKED"
            case PayrollGenerator::VARIABLES[3]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $days = $attendanceDetail->getNoOfDaysPresent($this->employeeId, $firstDayExp, $lastDayExp);
                $processedValue = $days;
                break;
//            "NO_OF_PAID_LEAVES"
            case PayrollGenerator::VARIABLES[4]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $leaves = $attendanceDetail->getleaveIdCount($this->employeeId, $firstDayExp, $lastDayExp);
                $leaves = Helper::extractDbData($leaves);

                $leaveInfo = [];
                $leaveInfo["PAID_LEAVE_NO"] = 0;
                $leaveInfo["UNPAID_LEAVE_NO"] = 0;
                $leaveMasterRepo = new LeaveMasterRepository($this->adapter);
                foreach ($leaves as $leave) {
                    if ($leaveMasterRepo->checkIfCashable($leave[AttendanceDetail::LEAVE_ID])) {
                        $leaveInfo["PAID_LEAVE_NO"] = $leaveInfo["PAID_LEAVE_NO"] + $leave[AttendanceDetail::LEAVE_ID . "_NO"];
                    } else {
                        $leaveInfo["UNPAID_LEAVE_NO"] = $leaveInfo["UNPAID_LEAVE_NO"] + $leave[AttendanceDetail::LEAVE_ID . "_NO"];
                    }
                }
                $processedValue = $leaveInfo["PAID_LEAVE_NO"];
                break;
//                "NO_OF_UNPAID_LEAVES"                
            case PayrollGenerator::VARIABLES[5]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $leaves = $attendanceDetail->getleaveIdCount($this->employeeId, $firstDayExp, $lastDayExp);
                $leaves = Helper::extractDbData($leaves);

                $unpaidLeaveCount = 0;
                $leaveMasterRepo = new LeaveMasterRepository($this->adapter);
                foreach ($leaves as $leave) {
                    if (!$leaveMasterRepo->checkIfCashable($leave[AttendanceDetail::LEAVE_ID])) {
                        $unpaidLeaveCount = $unpaidLeaveCount + $leave[AttendanceDetail::LEAVE_ID . "_NO"];
                    }
                }
                $processedValue = $unpaidLeaveCount;
                break;
//                "TOTAL_DAYS_TO_PAY"
            case PayrollGenerator::VARIABLES[6]:


                break;
//            "GENDER"
            case PayrollGenerator::VARIABLES[7]:

                break;
//            "EMP_TYPE"
            case PayrollGenerator::VARIABLES[8]:


                break;
//              "MARITUAL_STATUS"
            case PayrollGenerator::VARIABLES[9]:
                $maritualStatus = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::MARITAL_STATUS], [HrEmployees::EMPLOYEE_ID => $this->employeeId], null)[$this->employeeId];
                $processedValue = ($maritualStatus == "M") ? 1 : 0;
                break;
//            "TOTAL_DAYS_FROM_JOIN_DATE"
            case PayrollGenerator::VARIABLES[10]:


                break;
//            "SERVICE_TYPE"
            case PayrollGenerator::VARIABLES[11]:
                $serviceTypeId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::SERVICE_TYPE_ID], [HrEmployees::EMPLOYEE_ID => $this->employeeId], null)[$this->employeeId];
                if ($serviceTypeId == null) {
                    $processedValue = "";
                } else {
                    $serviceTypeCode = EntityHelper::getTableKVList($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_CODE], [ServiceType::SERVICE_TYPE_ID => $serviceTypeId], null)[$serviceTypeId];
                    $processedValue = $serviceTypeCode;
                }
                break;
//                "NO_OF_WORKING_DAYS_INC_HOLIDAYS"
            case PayrollGenerator::VARIABLES[12]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);

                $days = $attendanceDetail->getNoOfDaysInDayInterval($this->employeeId, $firstDayExp, $lastDayExp, false);

                $processedValue = $days;
                break;
//            "TOTAL_NO_OF_WORK_DAYS_INC_HOLIDAYS"
            case PayrollGenerator::VARIABLES[13]:
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $workingDays = $attendanceDetail->getTotalNoOfWorkingDays($firstDayExp, $lastDayExp);
                $processedValue = $workingDays;
                break;
//            "SALARY_REVIEW"
            case PayrollGenerator::VARIABLES[14]:
                $salaryDetailRepo = new SalaryDetailRepo($this->adapter);
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $salaryDetail = $salaryDetailRepo->fetchIfAvailable($firstDayExp, $lastDayExp, $this->employeeId);
                if ($salaryDetail->count() > 0) {
                    $salaryDetail = Helper::extractDbData($salaryDetail);
                    $effectiveDate = $salaryDetail[0]['EFFECTIVE_DATE'];
                    $dateObj = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $effectiveDate);
                    $processedValue = $dateObj->format('d');
                } else {
                    $processedValue = 0;
                }
                break;
//            "SALARY_REVIEW_OLD_SALARY"
            case PayrollGenerator::VARIABLES[14]:
                $salaryDetailRepo = new SalaryDetailRepo($this->adapter);
                $monthsRepo = new MonthRepository($this->adapter);
                $firstLastDate = $monthsRepo->fetchByMonthId($this->monthId);
                $attendanceDetail = new AttendanceDetailRepository($this->adapter);
                $firstDayExp = Helper::getExpressionDate($firstLastDate[Months::FROM_DATE]);
                $lastDayExp = Helper::getExpressionDate($firstLastDate[Months::TO_DATE]);
                $salaryDetail = $salaryDetailRepo->fetchIfAvailable($firstDayExp, $lastDayExp, $this->employeeId);
                if ($salaryDetail->count() > 0) {
                    $salaryDetail = Helper::extractDbData($salaryDetail);
                    $processedValue = $salaryDetail[0]['OLD_AMOUNT'];
                } else {
                    $processedValue = 0;
                }
                break;

            default:


                break;
        }

        return $processedValue;
    }

}
