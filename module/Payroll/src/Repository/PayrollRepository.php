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

    public function getNoOfDaysPresent($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(AD.EMPLOYEE_ID) AS NO_OF_DAYS_ABSENT
                FROM HRIS_ATTENDANCE_DETAIL AD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                AND AD.DAYOFF_FLAG = 'N'
                AND AD.HOLIDAY_ID IS NULL
                AND AD.IN_TIME    IS NOT NULL
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['NO_OF_DAYS_ABSENT'];
    }

    public function getNoOfPaidLeaves($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(EL.LEAVE_ID) AS NO_OF_PAID_LEAVES
                FROM
                  (SELECT LEAVE_ID
                  FROM HRIS_ATTENDANCE_DETAIL AD,
                    (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                    ) M
                  WHERE AD.EMPLOYEE_ID = {$employeeId}
                  AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                  AND AD.DAYOFF_FLAG = 'N'
                  AND AD.HOLIDAY_ID IS NULL
                  AND AD.LEAVE_ID   IS NOT NULL
                  ) EL
                JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LMS.LEAVE_ID = EL.LEAVE_ID)
                WHERE LMS.PAID   = 'Y'
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['NO_OF_PAID_LEAVES'];
    }

    public function getNoOfUnpaidLeaves($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(EL.LEAVE_ID) AS NO_OF_PAID_LEAVES
                FROM
                  (SELECT LEAVE_ID
                  FROM HRIS_ATTENDANCE_DETAIL AD,
                    (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                    ) M
                  WHERE AD.EMPLOYEE_ID = {$employeeId}
                  AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                  AND AD.DAYOFF_FLAG = 'N'
                  AND AD.HOLIDAY_ID IS NULL
                  AND AD.LEAVE_ID   IS NOT NULL
                  ) EL
                JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LMS.LEAVE_ID = EL.LEAVE_ID)
                WHERE LMS.PAID   = 'N'
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['NO_OF_PAID_LEAVES'];
    }

    public function getEmployeeGender($employeeId) {
        $sql = "
                SELECT GENDER_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID={$employeeId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['GENDER_ID'];
    }

    public function getEmployeeServiceType($employeeId) {
        $sql = "
                SELECT SERVICE_TYPE_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID={$employeeId}                
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['SERVICE_TYPE_ID'];
    }

    public function getEmployeeMaritualStatus($employeeId) {
        $sql = "
                SELECT (CASE WHEN MARITAL_STATUS = 'M' THEN 1 ELSE 0 END) AS MARITAL_STATUS FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID={$employeeId}                
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['MARITAL_STATUS'];
    }

    public function getNoOfWorkingDaysIncDayOffAndHoliday(int $employeeId, $monthId) {
        $sql = "
                SELECT COUNT(AD.EMPLOYEE_ID) AS NO_OF_WORKING_DAYS
                FROM HRIS_ATTENDANCE_DETAIL AD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['NO_OF_WORKING_DAYS'];
    }

    public function getNoOfDaysWorkedIncDayOffAndHoliday(int $employeeId, $monthId) {
        $sql = "
                SELECT COUNT(AD.EMPLOYEE_ID) AS NO_OF_DAYS_WORKED
                FROM HRIS_ATTENDANCE_DETAIL AD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                AND (TRUNC(ATTENDANCE_DT) BETWEEN M.FROM_DATE AND TO_DATE)
                AND AD.LEAVE_ID IS NULL
                AND AD.IN_TIME IS NOT NULL
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['NO_OF_DAYS_WORKED'];
    }

    public function getEmployeeSalaryReviewDay(int $employeeId, $monthId) {
        $sql = "
                SELECT TRUNC(SD.EFFECTIVE_DATE)- TRUNC(M.FROM_DATE) AS REVIEW_DAY
                FROM HRIS_SALARY_DETAIL SD,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M
                WHERE (SD.EFFECTIVE_DATE BETWEEN M.FROM_DATE AND M.TO_DATE)
                AND SD.EMPLOYEE_ID={$employeeId}
                AND SD.STATUS     ='E'
                AND ROWNUM        =1
                ORDER BY SD.EFFECTIVE_DATE DESC
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return ($result != null) ? $result['REVIEW_DAY'] : -1;
    }

    public function getMonthDays($employeeId, $monthId) {
        $sql = "
            SELECT TRUNC(TO_DATE)- TRUNC(FROM_DATE) AS MONTH_DAYS
            FROM HRIS_MONTH_CODE
            WHERE MONTH_ID= {$monthId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['MONTH_DAYS'];
    }

    public function getPresentDays($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) AS PRESENT_DAYS
                FROM HRIS_ATTENDANCE_DETAIL A,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                  ) M
                WHERE A.EMPLOYEE_ID = {$employeeId}
                AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                AND OVERALL_STATUS IN ('PR','TV','TN','VP','TP','BA','LA')
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['PRESENT_DAYS'];
    }

    public function getAbsentDays($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) AS ABSENT_DAYS
                FROM HRIS_ATTENDANCE_DETAIL A,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                  ) M
                WHERE A.EMPLOYEE_ID = {$employeeId}
                AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                AND OVERALL_STATUS = 'AB'
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['ABSENT_DAYS'];
    }

    public function getPaidLeaves($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) PAID_LEAVES
                FROM HRIS_LEAVE_MASTER_SETUP L
                JOIN
                  (SELECT *
                  FROM HRIS_ATTENDANCE_DETAIL A,
                    (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                    ) M
                  WHERE A.EMPLOYEE_ID = {$employeeId}
                  AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                  AND OVERALL_STATUS = 'LV'
                  ) A
                ON (A.LEAVE_ID = L.LEAVE_ID)
                WHERE L.PAID   = 'Y'
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['PAID_LEAVES'];
    }

    public function getUnpaidLeaves($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) AS UNPAID_LEAVES
                FROM HRIS_LEAVE_MASTER_SETUP L
                JOIN
                  (SELECT *
                  FROM HRIS_ATTENDANCE_DETAIL A,
                    (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                    ) M
                  WHERE A.EMPLOYEE_ID = {$employeeId}
                  AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                  AND OVERALL_STATUS = 'LV'
                  ) A
                ON (A.LEAVE_ID = L.LEAVE_ID)
                WHERE L.PAID   = 'N'
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['UNPAID_LEAVES'];
    }

    public function getDayoffs($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) AS DAY_OFFS
                FROM HRIS_ATTENDANCE_DETAIL A,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                  ) M
                WHERE A.EMPLOYEE_ID = {$employeeId}
                AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                AND OVERALL_STATUS IN ('DO','WD') 
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['DAY_OFFS'];
    }

    public function getHolidays($employeeId, $monthId) {
        $sql = "
                SELECT COUNT(*) AS HOLIDAYS
                FROM HRIS_ATTENDANCE_DETAIL A,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                  ) M
                WHERE A.EMPLOYEE_ID = {$employeeId}
                AND (ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                AND OVERALL_STATUS IN ('HD','WH') 
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['HOLIDAYS'];
    }

    public function getDaysFromJoinDate($employeeId, $monthId) {
        $sql = "
                SELECT (TRUNC(M.FROM_DATE)- TRUNC(JOIN_DATE)) AS DAYS_FROM_JOIN_DATE
                FROM HRIS_EMPLOYEES E,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M WHERE E.EMPLOYEE_ID={$employeeId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['DAYS_FROM_JOIN_DATE'];
    }

    public function getDaysFromPermanentDate($employeeId, $monthId) {
        $sql = "
                SELECT (TRUNC(M.FROM_DATE)- TRUNC(PERMANENT_DATE)) AS DAYS_FROM_PERMANENT_DATE
                FROM HRIS_EMPLOYEES E,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID= {$monthId}
                  ) M WHERE E.EMPLOYEE_ID={$employeeId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current()['DAYS_FROM_PERMANENT_DATE'];
    }

    public function isMale($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN GENDER_ID = 1
                    THEN 1
                    ELSE 0
                  END) AS IS_MALE
                FROM HRIS_EMPLOYEES
                WHERE EMPLOYEE_ID = {$employeeId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['IS_MALE'];
    }

    public function isFemale($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN GENDER_ID = 2
                    THEN 1
                    ELSE 0
                  END) AS IS_FEMALE
                FROM HRIS_EMPLOYEES
                WHERE EMPLOYEE_ID = {$employeeId}
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['IS_FEMALE'];
    }

    public function isMarried($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN MARITAL_STATUS = 'M'
                    THEN 1
                    ELSE 0
                  END) AS IS_MARRIED
                FROM HRIS_EMPLOYEES
                WHERE EMPLOYEE_ID={$employeeId}              
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        return $result['IS_MARRIED'];
    }

    public function isPermanent($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =1
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                      ) M
                    WHERE JH.EMPLOYEE_ID = {$employeeId}
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  WHERE ROWNUM =1
                  )           
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function isProbation($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =2
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                      ) M
                    WHERE JH.EMPLOYEE_ID = {$employeeId}
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  WHERE ROWNUM =1
                  )           
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function isContract($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =3
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                      ) M
                    WHERE JH.EMPLOYEE_ID = {$employeeId}
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  WHERE ROWNUM =1
                  )           
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function isTemporary($employeeId, $monthId) {
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =4
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId}
                      ) M
                    WHERE JH.EMPLOYEE_ID = {$employeeId}
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  WHERE ROWNUM =1
                  )           
                ";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        $result = $rawResult->current();
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

}
