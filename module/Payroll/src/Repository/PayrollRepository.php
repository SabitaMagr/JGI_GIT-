<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class PayrollRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchEmployeeList() {
        $sql = "
                SELECT E.EMPLOYEE_ID,
                  CONCAT(CONCAT(CONCAT(INITCAP(TRIM(E.FIRST_NAME)),' '),
                  CASE
                    WHEN E.MIDDLE_NAME IS NOT NULL
                    THEN CONCAT(INITCAP(TRIM(E.MIDDLE_NAME)), ' ')
                    ELSE ''
                  END ),INITCAP(TRIM(E.LAST_NAME))) AS FULL_NAME
                FROM HRIS_EMPLOYEES E
                WHERE E.JOIN_DATE <= TRUNC(SYSDATE)
                AND E.RETIRED_FLAG ='N'
                AND IS_ADMIN       ='N'
                AND STATUS         ='E'
                ";
        $employeeListRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($employeeListRaw);
    }

    public function fetchFiscalYears() {
        $sql = "
                SELECT FISCAL_YEAR_ID                                                     AS FISCAL_YEAR_ID,
                  CONCAT(CONCAT(TO_CHAR(START_DATE,'YYYY'),'-'),TO_CHAR(END_DATE,'YYYY')) AS NAME,
                  START_DATE                                                              AS START_DATE,
                  END_DATE                                                                AS END_DATE
                FROM HRIS_FISCAL_YEARS ORDER BY START_DATE DESC
                ";
        $fiscalYearsRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($fiscalYearsRaw);
    }

    public function fetchBasicSalary($employeeId) {
        $sql = "
                SELECT NVL(SALARY,0) AS SALARY
                FROM HRIS_EMPLOYEES
                WHERE EMPLOYEE_ID={$employeeId}
                ";
        $basicSalaryRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $basicSalaryRaw->current()['SALARY'];
    }

    public function getNoOfWorkingDays(int $employeeId, $monthId) {
        $sql = "
                SELECT COUNT(AD.EMPLOYEE_ID) AS NO_OF_WORKING_DAYS
                FROM HRIS_ATTENDANCE_DETAIL AD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                AND AD.DAYOFF_FLAG = 'N'
                AND AD.HOLIDAY_ID    IS NULL
                ";
        $workingDaysCountRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $workingDaysCountRaw->current()['NO_OF_WORKING_DAYS'];
    }

    public function getNoOfDaysAbsent($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(AD.EMPLOYEE_ID) AS NO_OF_DAYS_ABSENT
                FROM HRIS_ATTENDANCE_DETAIL AD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                AND AD.DAYOFF_FLAG = 'N'
                AND AD.HOLIDAY_ID IS NULL
                AND AD.IN_TIME    IS NULL
                ";
        $workingDaysCountRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $workingDaysCountRaw->current()['NO_OF_DAYS_ABSENT'];
    }

}
