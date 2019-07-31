<?php

namespace Cron\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class CronRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAbsentOrLate() {
        $sql = "SELECT DISTINCT AD.EMPLOYEE_ID,
  E.FULL_NAME                       AS EMPLOYEE_NAME,
  TO_CHAR(AD.IN_TIME, 'HH:MM AM')   AS IN_TIME,
  TO_CHAR(S.START_TIME, 'HH:MM AM') AS START_TIME,
  TO_CHAR(AD.OUT_TIME, 'HH:MM AM')  AS OUT_TIME,
  TO_CHAR(S.END_TIME, 'HH:MM AM')   AS END_TIME,
  AD.OVERALL_STATUS,
  AD.LATE_STATUS,
  E.EMAIL_OFFICIAL AS EMPLOYEE_MAIL,
  E.BRANCH_ID,
  B.BRANCH_MANAGER_ID,
  EE.FULL_NAME      AS MANAGER_NAME,
  EE.EMAIL_OFFICIAL AS MANAGER_MAIL,
  AD.ATTENDANCE_DT
FROM HRIS_ATTENDANCE_DETAIL AD
LEFT JOIN HRIS_EMPLOYEES E
ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
LEFT JOIN HRIS_BRANCHES B
ON (E.BRANCH_ID = B.BRANCH_ID)
LEFT JOIN HRIS_EMPLOYEES EE
ON (B.BRANCH_MANAGER_ID = EE.EMPLOYEE_ID)
LEFT JOIN HRIS_SHIFTS S
  ON (AD.SHIFT_ID        = S.SHIFT_ID)
WHERE AD.ATTENDANCE_DT = '25-JAN-19'
AND (AD.LATE_STATUS  = 'L' OR AD.OVERALL_STATUS = 'AB') 
AND E.STATUS = 'E'
ORDER BY AD.EMPLOYEE_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchMissedOrEarlyOut() {
        $sql = "SELECT DISTINCT AD.EMPLOYEE_ID,
  E.FULL_NAME                       AS EMPLOYEE_NAME,
  TO_CHAR(AD.IN_TIME, 'HH:MM AM')   AS IN_TIME,
  TO_CHAR(S.START_TIME, 'HH:MM AM') AS START_TIME,
  TO_CHAR(AD.OUT_TIME, 'HH:MM AM')  AS OUT_TIME,
  TO_CHAR(S.END_TIME, 'HH:MM AM')   AS END_TIME,
  AD.OVERALL_STATUS,
  AD.LATE_STATUS,
  E.EMAIL_OFFICIAL AS EMPLOYEE_MAIL,
  E.BRANCH_ID,
  B.BRANCH_MANAGER_ID,
  EE.FULL_NAME      AS MANAGER_NAME,
  EE.EMAIL_OFFICIAL AS MANAGER_MAIL,
  AD.ATTENDANCE_DT
FROM HRIS_ATTENDANCE_DETAIL AD
LEFT JOIN HRIS_EMPLOYEES E
ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
LEFT JOIN HRIS_BRANCHES B
ON (E.BRANCH_ID = B.BRANCH_ID)
LEFT JOIN HRIS_EMPLOYEES EE
ON (B.BRANCH_MANAGER_ID = EE.EMPLOYEE_ID)
LEFT JOIN HRIS_SHIFTS S
ON (AD.SHIFT_ID        = S.SHIFT_ID)
WHERE AD.ATTENDANCE_DT = '25-JAN-19'
AND AD.LATE_STATUS    IN ('E', 'B', 'X', 'Y')
ORDER BY AD.EMPLOYEE_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function doReattendance() {
        print_r('inside reattendance');
        $sql = "BEGIN 
                HRIS_REATTENDANCE(trunc(sysdate));
                END; 
                ";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        print_r('out of reattendance');
        return ;
//        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

}
