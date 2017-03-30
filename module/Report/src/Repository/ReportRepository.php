<?php

namespace Report\Repository;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class ReportRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function departmentWiseEmployeeMonthReport($departmentId) {
        $sql = <<<EOT
SELECT J.*,
  JE.FIRST_NAME AS FIRST_NAME,
    JE.MIDDLE_NAME AS MIDDLE_NAME,
    JE.LAST_NAME AS LAST_NAME,
  JM.MONTH_EDESC
FROM
  (SELECT I.EMPLOYEE_ID,
    I.MONTH_ID ,
    SUM(I.ON_LEAVE)    AS ON_LEAVE,
    SUM (I.IS_PRESENT) AS IS_PRESENT,
    SUM(I.IS_ABSENT)   AS IS_ABSENT
  FROM
    (SELECT E.EMPLOYEE_ID AS EMPLOYEE_ID,
      (SELECT M.MONTH_ID
      FROM HRIS_MONTH_CODE M
      WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
      ) AS MONTH_ID,
      (
      CASE AD.LEAVE_ID
        WHEN NULL
        THEN 1
        ELSE 0
      END) AS ON_LEAVE,
      (
      CASE
        WHEN AD.LEAVE_ID   IS NULL
        AND AD.HOLIDAY_ID  IS NULL
        AND AD.TRAINING_ID IS NULL
        AND AD.TRAVEL_ID   IS NULL
        AND AD.IN_TIME     IS NOT NULL
        THEN 1
        ELSE 0
      END) AS IS_PRESENT,
      (
      CASE
        WHEN AD.LEAVE_ID   IS NULL
        AND AD.HOLIDAY_ID  IS NULL
        AND AD.TRAINING_ID IS NULL
        AND AD.TRAVEL_ID   IS NULL
        AND AD.IN_TIME     IS NULL
        THEN 1
        ELSE 0
      END) AS IS_ABSENT
    FROM HRIS_ATTENDANCE_DETAIL AD
    JOIN HRIS_EMPLOYEES E
    ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
    WHERE E.DEPARTMENT_ID=$departmentId
    ) I
  GROUP BY I.EMPLOYEE_ID,
    I.MONTH_ID
  ) J
JOIN HRIS_EMPLOYEES JE
ON (J.EMPLOYEE_ID = JE.EMPLOYEE_ID)
JOIN HRIS_MONTH_CODE JM
ON (J.MONTH_ID = JM.MONTH_ID)
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function departmentMonthReport() {
        $sql = <<<EOT
SELECT J.*,
  JD.DEPARTMENT_NAME AS DEPARTMENT_NAME,
  JM.MONTH_EDESC
FROM
  (SELECT I.DEPARTMENT_ID ,
    I.MONTH_ID ,
    SUM(I.ON_LEAVE)    AS ON_LEAVE,
    SUM (I.IS_PRESENT) AS IS_PRESENT,
    SUM(I.IS_ABSENT)   AS IS_ABSENT
  FROM
    (SELECT D.DEPARTMENT_ID AS DEPARTMENT_ID,
      (SELECT M.MONTH_ID
      FROM HRIS_MONTH_CODE M
      WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
      ) AS MONTH_ID,
      (
      CASE AD.LEAVE_ID
        WHEN NULL
        THEN 1
        ELSE 0
      END) AS ON_LEAVE,
      (
      CASE
        WHEN AD.LEAVE_ID   IS NULL
        AND AD.HOLIDAY_ID  IS NULL
        AND AD.TRAINING_ID IS NULL
        AND AD.TRAVEL_ID   IS NULL
        AND AD.IN_TIME     IS NOT NULL
        THEN 1
        ELSE 0
      END) AS IS_PRESENT,
      (
      CASE
        WHEN AD.LEAVE_ID   IS NULL
        AND AD.HOLIDAY_ID  IS NULL
        AND AD.TRAINING_ID IS NULL
        AND AD.TRAVEL_ID   IS NULL
        AND AD.IN_TIME     IS NULL
        THEN 1
        ELSE 0
      END) AS IS_ABSENT
    FROM HRIS_ATTENDANCE_DETAIL AD
    JOIN HRIS_EMPLOYEES E
    ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
    JOIN HRIS_DEPARTMENTS D
    ON(E.DEPARTMENT_ID=D.DEPARTMENT_ID)
    ) I
  GROUP BY I.DEPARTMENT_ID,
    I.MONTH_ID
  ) J
JOIN HRIS_DEPARTMENTS JD
ON (J.DEPARTMENT_ID = JD.DEPARTMENT_ID)
JOIN HRIS_MONTH_CODE JM
ON (J.MONTH_ID = JM.MONTH_ID)                
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
