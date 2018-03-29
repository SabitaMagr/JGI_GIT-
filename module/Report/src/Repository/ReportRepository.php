<?php

namespace Report\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveMaster;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class ReportRepository extends HrisRepository {

    public function employeeWiseDailyReport($employeeId) {
        $sql = <<<EOT
            SELECT R.*,
              M.MONTH_EDESC,
              TRUNC(R.ATTENDANCE_DT)-TRUNC(M.FROM_DATE)+1 AS DAY_COUNT
            FROM
              (SELECT AD.ATTENDANCE_DT                AS ATTENDANCE_DT,
                TO_CHAR(AD.ATTENDANCE_DT,'MONDDYYYY') AS FORMATTED_ATTENDANCE_DT,
                (SELECT M.MONTH_ID
                FROM HRIS_MONTH_CODE M
                WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
                ) AS MONTH_ID,
                (
              CASE 
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NULL
                AND AD.LEAVE_ID IS NOT NULL
                THEN 1
                ELSE 0
              END) AS ON_LEAVE,
                (
              CASE
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.LEAVE_ID    IS NULL
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NOT NULL
                THEN 1
                ELSE 0
              END) AS IS_PRESENT,
                (
              CASE
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.LEAVE_ID   IS NULL
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NULL
                THEN 1
                ELSE 0
              END) AS IS_ABSENT,
                (
              CASE
                WHEN AD.LEAVE_ID   IS NULL
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NULL 
                  AND  AD.DAYOFF_FLAG='Y'
                THEN 1
                ELSE 0
              END) AS IS_DAYOFF
              FROM HRIS_ATTENDANCE_DETAIL AD
              WHERE AD.EMPLOYEE_ID = $employeeId
              ) R
            JOIN HRIS_MONTH_CODE M
            ON (M.MONTH_ID = R.MONTH_ID)
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function departmentWiseDailyReport(int $monthId, int $departmentId = null, int $branchId = null) {
        $sql = <<<EOT
                      SELECT 
                      TRUNC(AD.ATTENDANCE_DT)-TRUNC(M.FROM_DATE)+1                              AS DAY_COUNT, 
                      E.EMPLOYEE_ID                                                             AS EMPLOYEE_ID ,
                      E.FIRST_NAME                                                                   AS FIRST_NAME,
                      E.MIDDLE_NAME                                                                  AS MIDDLE_NAME,
                      E.LAST_NAME                                                                    AS LAST_NAME,
                      CONCAT(CONCAT(CONCAT(E.FIRST_NAME,' '),CONCAT(E.MIDDLE_NAME, '')),E.LAST_NAME) AS FULL_NAME,
                      AD.ATTENDANCE_DT                                                               AS ATTENDANCE_DT,
                      (
                      CASE 
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL
                        AND AD.LEAVE_ID IS NOT NULL
                        THEN 1
                        ELSE 0
                      END) AS ON_LEAVE,
                      (
                      CASE
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.LEAVE_ID    IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NOT NULL
                        THEN 1
                        ELSE 0
                      END) AS IS_PRESENT,
                      (
                      CASE
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.LEAVE_ID   IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL
                        THEN 1
                        ELSE 0
                      END) AS IS_ABSENT,
                      (
                      CASE
                        WHEN AD.LEAVE_ID   IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL 
                          AND  AD.DAYOFF_FLAG='Y'
                        THEN 1
                        ELSE 0
                      END) AS IS_DAYOFF
                    FROM HRIS_ATTENDANCE_DETAIL AD
                    JOIN HRIS_EMPLOYEES E
                    ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID),
                      ( SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID=$monthId
                      ) M
                    WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
                    AND E.DEPARTMENT_ID=$departmentId
                    ORDER BY AD.ATTENDANCE_DT,
                      E.EMPLOYEE_ID
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function departmentWiseEmployeeMonthReport($departmentId) {
        $sql = <<<EOT
                SELECT J.*,
                  JE.FIRST_NAME AS FIRST_NAME,
                    JE.MIDDLE_NAME AS MIDDLE_NAME,
                    JE.LAST_NAME AS LAST_NAME,
                    CONCAT(CONCAT(CONCAT(JE.FIRST_NAME,' '),CONCAT(JE.MIDDLE_NAME, '')),JE.LAST_NAME) AS FULL_NAME,
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
                  CASE 
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.HOLIDAY_ID  IS NULL
                    AND AD.TRAINING_ID IS NULL
                    AND AD.TRAVEL_ID   IS NULL
                    AND AD.IN_TIME     IS NULL
                    AND AD.LEAVE_ID IS NOT NULL
                    THEN 1
                    ELSE 0
                  END) AS ON_LEAVE,
                      (
                  CASE
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.LEAVE_ID    IS NULL
                    AND AD.HOLIDAY_ID  IS NULL
                    AND AD.TRAINING_ID IS NULL
                    AND AD.TRAVEL_ID   IS NULL
                    AND AD.IN_TIME     IS NOT NULL
                    THEN 1
                    ELSE 0
                  END) AS IS_PRESENT,
                     (
                  CASE
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.LEAVE_ID   IS NULL
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

    public function branchWiseEmployeeMonthReport($branchId) {
        $sql = <<<EOT
                SELECT J.*,
                  JE.FIRST_NAME AS FIRST_NAME,
                    JE.MIDDLE_NAME AS MIDDLE_NAME,
                    JE.LAST_NAME AS LAST_NAME,
                    CONCAT(CONCAT(CONCAT(JE.FIRST_NAME,' '),CONCAT(JE.MIDDLE_NAME, '')),JE.LAST_NAME) AS FULL_NAME,
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
                  CASE 
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.HOLIDAY_ID  IS NULL
                    AND AD.TRAINING_ID IS NULL
                    AND AD.TRAVEL_ID   IS NULL
                    AND AD.IN_TIME     IS NULL
                    AND AD.LEAVE_ID IS NOT NULL
                    THEN 1
                    ELSE 0
                  END) AS ON_LEAVE,
                      (
                  CASE
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.LEAVE_ID    IS NULL
                    AND AD.HOLIDAY_ID  IS NULL
                    AND AD.TRAINING_ID IS NULL
                    AND AD.TRAVEL_ID   IS NULL
                    AND AD.IN_TIME     IS NOT NULL
                    THEN 1
                    ELSE 0
                  END) AS IS_PRESENT,
                     (
                  CASE
                    WHEN AD.DAYOFF_FLAG ='N'
                    AND AD.LEAVE_ID   IS NULL
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
                    WHERE E.BRANCH_ID=$branchId
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
              CASE 
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NULL
                AND AD.LEAVE_ID IS NOT NULL
                THEN 1
                ELSE 0
              END) AS ON_LEAVE,
                  (
              CASE
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.LEAVE_ID    IS NULL
                AND AD.HOLIDAY_ID  IS NULL
                AND AD.TRAINING_ID IS NULL
                AND AD.TRAVEL_ID   IS NULL
                AND AD.IN_TIME     IS NOT NULL
                THEN 1
                ELSE 0
              END) AS IS_PRESENT,
                 (
              CASE
                WHEN AD.DAYOFF_FLAG ='N'
                AND AD.LEAVE_ID   IS NULL
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

    public function getCompanyBranchDepartment() {
        $sql = <<<EOT
            SELECT C.COMPANY_ID,
              C.COMPANY_NAME,
              B.BRANCH_ID,
              B.BRANCH_NAME,
              D.DEPARTMENT_ID,
              D.DEPARTMENT_NAME
            FROM HRIS_COMPANY C
            LEFT JOIN HRIS_BRANCHES B
            ON (C.COMPANY_ID =B.COMPANY_ID)
            LEFT  JOIN HRIS_DEPARTMENTS D
            ON (D.BRANCH_ID=B.BRANCH_ID)
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getMonthList() {
        $sql = <<<EOT
            SELECT AM.MONTH_ID,M.MONTH_EDESC FROM
            (SELECT  UNIQUE (SELECT M.MONTH_ID
                FROM HRIS_MONTH_CODE M
                WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
                ) AS MONTH_ID
            FROM HRIS_ATTENDANCE_DETAIL AD) AM JOIN HRIS_MONTH_CODE M ON (M.MONTH_ID=AM.MONTH_ID) 
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getEmployeeList() {
        $sql = <<<EOT
            SELECT E.EMPLOYEE_ID                                                             AS EMPLOYEE_ID,
              E.FIRST_NAME                                                                   AS FIRST_NAME,
              E.MIDDLE_NAME                                                                  AS MIDDLE_NAME,
              E.LAST_NAME                                                                    AS LAST_NAME,
              CONCAT(CONCAT(CONCAT(E.FIRST_NAME,' '),CONCAT(E.MIDDLE_NAME, '')),E.LAST_NAME) AS FULL_NAME,
              E.COMPANY_ID                                                                   AS COMPANY_ID,
              E.BRANCH_ID                                                                    AS BRANCH_ID,
              E.DEPARTMENT_ID                                                                AS DEPARTMENT_ID
            FROM HRIS_EMPLOYEES E
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function reportWithOT($data) {
        $fromCondition = "";
        $toCondition = "";

        $otFromCondition = "";
        $otToCondition = "";

        $condition = EntityHelper::getSearchConditon($data['companyId'], $data['branchId'], $data['departmentId'], $data['positionId'], $data['designationId'], $data['serviceTypeId'], $data['serviceEventTypeId'], $data['employeeTypeId'], $data['employeeId'], $data['genderId'], $data['locationId']);

        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND A.ATTENDANCE_DT >= {$fromDate->getExpression()}";
            $otFromCondition = "AND OVERTIME_DATE >= {$fromDate->getExpression()} ";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND A.ATTENDANCE_DT <= {$toDate->getExpression()}";
            $otToCondition = "AND OVERTIME_DATE <= {$toDate->getExpression()} ";
        }



        $sql = <<<EOT
            SELECT 
              C.COMPANY_NAME,
              D.DEPARTMENT_NAME,
              A.EMPLOYEE_ID,
              E.FULL_NAME,
              A.DAYOFF,
              A.PRESENT,
              A.HOLIDAY,
              A.LEAVE,
              A.PAID_LEAVE,
              A.UNPAID_LEAVE,
              A.ABSENT,
              NVL(ROUND(OT.TOTAL_MIN/60,2),0) AS OVERTIME_HOUR,
              A.TRAVEL,
              A.TRAINING,
              A.WORK_ON_HOLIDAY,
              A.WORK_ON_DAYOFF
            FROM
              (SELECT A.EMPLOYEE_ID,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS IN( 'DO','WD')
                  THEN 1
                  ELSE 0
                END) AS DAYOFF,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS IN ('PR','BA','LA','TV','VP','TN','TP')
                  THEN 1
                  ELSE 0
                END) AS PRESENT,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS IN ('HD','WH')
                  THEN 1
                  ELSE 0
                END) AS HOLIDAY,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS IN ('LV','LP')
                  THEN 1
                  ELSE 0
                END) AS LEAVE,
                SUM(
                CASE
                  WHEN L.PAID = 'Y'
                  THEN 1
                  ELSE 0
                END) AS PAID_LEAVE,
                SUM(
                CASE
                  WHEN L.PAID = 'N'
                  THEN 1
                  ELSE 0
                END) AS UNPAID_LEAVE,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS = 'AB'
                  THEN 1
                  ELSE 0
                END) AS ABSENT,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS= 'TV'
                  THEN 1
                  ELSE 0
                END) AS TRAVEL,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS ='TN'
                  THEN 1
                  ELSE 0
                END) AS TRAINING,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS = 'WH'
                  THEN 1
                  ELSE 0
                END) WORK_ON_HOLIDAY,
                SUM(
                CASE
                  WHEN A.OVERALL_STATUS ='WD'
                  THEN 1
                  ELSE 0
                END) WORK_ON_DAYOFF
              FROM HRIS_ATTENDANCE_DETAIL A
              LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
              ON (A.LEAVE_ID= L.LEAVE_ID)
              WHERE 1=1
              {$fromCondition}
              {$toCondition}
              GROUP BY A.EMPLOYEE_ID
              ) A
            LEFT JOIN HRIS_EMPLOYEES E
            ON(A.EMPLOYEE_ID = E.EMPLOYEE_ID)
            LEFT JOIN HRIS_COMPANY C
            ON(E.COMPANY_ID= C.COMPANY_ID)
            LEFT JOIN HRIS_DEPARTMENTS D
            ON (E.DEPARTMENT_ID= D.DEPARTMENT_ID)
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                SUM(TOTAL_HOUR) AS TOTAL_MIN
              FROM HRIS_OVERTIME
              WHERE 1=1
              {$otFromCondition}
              {$otToCondition}
              AND STATUS= 'AP'
              GROUP BY EMPLOYEE_ID
              ) OT
            ON (A.EMPLOYEE_ID = OT.EMPLOYEE_ID)
              WHERE 1=1 
              {$condition}
            ORDER BY C.COMPANY_NAME,
              D.DEPARTMENT_NAME,E.FULL_NAME
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchAllLeave() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LeaveMaster::class, [LeaveMaster::LEAVE_ENAME], NULL, NULL, NULL, NULL, 'L', false, false, null, ["REPLACE(l.leave_ename, ' ', '') AS LEAVE_TRIM_ENAME"]), false);
        $select->from(['L' => LeaveMaster::TABLE_NAME]);
        $select->where(["L.STATUS='E'"]);
        $select->order(LeaveMaster::LEAVE_ID . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
//        return $result;
    }

    private function leaveIn($allLeave) {
        $leaveCount = count($allLeave);

        $leaveIn = "";
        $i = 1;
        foreach ($allLeave as $leave) {
            $leaveIn .= $leave['LEAVE_ID'];
            if ($i < $leaveCount) {
                $leaveIn .= ',';
            }
            $i++;
        }
        return $leaveIn;
    }

    private function convertLeaveIdToName($allLeave, $leaveData, $name) {
        $columnData = [];
        foreach ($leaveData as $report) {
            $tempData = [
//                'EMPLOYEE_ID' => $report['EMPLOYEE_ID'],
                'NAME' => $report[$name]
            ];
            foreach ($allLeave as $leave) {
                $tempData[$leave['LEAVE_TRIM_ENAME']] = $report[$leave['LEAVE_ID']];
            }
            array_push($columnData, $tempData);
        }
//            print_r($columnData);
//            die();
        return $columnData;
    }

    public function filterLeaveReportEmployee($data) {

        $allLeave = $this->fetchAllLeave();
        $leaveIn = $this->leaveIn($allLeave);

        $companyCondition = "";
        $branchCondition = "";
        $departmentCondition = "";
        $designationCondition = "";
        $positionCondition = "";
        $serviceTypeCondition = "";
        $serviceEventTypeConditon = "";
        $employeeCondition = "";
        $employeeTypeCondition = "";

        $fromCondition = "";
        $toCondition = "";

        if (isset($data['companyId']) && $data['companyId'] != null && $data['companyId'] != -1) {
            $companyCondition = "AND E.COMPANY_ID = {$data['companyId']}";
        }
        if (isset($data['branchId']) && $data['branchId'] != null && $data['branchId'] != -1) {
            $branchCondition = "AND E.BRANCH_ID = {$data['branchId']}";
        }
        if (isset($data['departmentId']) && $data['departmentId'] != null && $data['departmentId'] != -1) {
            $departmentCondition = "AND E.DEPARTMENT_ID = {$data['departmentId']}";
        }
        if (isset($data['designationId']) && $data['designationId'] != null && $data['designationId'] != -1) {
            $designationCondition = "AND E.DESIGNATION_ID = {$data['designationId']}";
        }
        if (isset($data['positionId']) && $data['positionId'] != null && $data['positionId'] != -1) {
            $positionCondition = "AND E.POSITION_ID = {$data['positionId']}";
        }
        if (isset($data['serviceTypeId']) && $data['serviceTypeId'] != null && $data['serviceTypeId'] != -1) {
            $serviceTypeCondition = "AND E.SERVICE_TYPE_ID = {$data['serviceTypeId']}";
        }
        if (isset($data['serviceEventTypeId']) && $data['serviceEventTypeId'] != null && $data['serviceEventTypeId'] != -1) {
            $serviceEventTypeConditon = "AND E.SERVICE_EVENT_TYPE_ID = {$data['serviceEventTypeId']}";
        }
        if (isset($data['employeeId']) && $data['employeeId'] != null && $data['employeeId'] != -1) {
            $employeeCondition = "AND E.EMPLOYEE_ID = {$data['employeeId']}";
        }
        if (isset($data['employeeTypeId']) && $data['employeeTypeId'] != null && $data['employeeTypeId'] != -1) {
            $employeeTypeCondition = "AND E.EMPLOYEE_TYPE = '{$data['employeeTypeId']}'";
        }
        $condition = $companyCondition . $branchCondition . $departmentCondition . $designationCondition . $positionCondition . $serviceTypeCondition . $serviceEventTypeConditon . $employeeCondition . $employeeTypeCondition;

        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND AD.ATTENDANCE_DT >= {$fromDate->getExpression()}";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND AD.ATTENDANCE_DT <= {$toDate->getExpression()}";
        }
        $dateCondition = $fromCondition . $toCondition;
        $sql = <<<EOT
                
                            SELECT
                e.full_name,
                leave.*
            FROM
                hris_employees e
                LEFT JOIN (
                    select * from (SELECT
                        ad.employee_id,
                        ad.leave_id,
                        COUNT(ad.leave_id) AS leave_days
                    FROM
                        hris_attendance_detail ad
                    WHERE
                            ad.leave_id IS NOT NULL
                            {$dateCondition}
                    GROUP BY
                        ad.employee_id,
                        ad.leave_id)PIVOT ( SUM ( leave_days )
                        FOR leave_id
                        IN ( {$leaveIn})
                    )
                ) leave ON (
                    e.employee_id = leave.employee_id
                )
            WHERE
                1 = 1
              {$condition}
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedResult = Helper::extractDbData($result);
        return $this->convertLeaveIdToName($allLeave, $extractedResult, 'FULL_NAME');
    }

    public function filterLeaveReportBranch($data) {

        $allLeave = $this->fetchAllLeave();
        $leaveIn = $this->leaveIn($allLeave);
        $fromCondition = "";
        $toCondition = "";


        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND AD.ATTENDANCE_DT >= {$fromDate->getExpression()}";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND AD.ATTENDANCE_DT <= {$toDate->getExpression()}";
        }
        $dateCondition = $fromCondition . $toCondition;
        $sql = <<<EOT
                
                SELECT BB.BRANCH_NAME,AA.* FROM (SELECT
                          *
                        FROM
                          (
                            SELECT
                              AD.LEAVE_ID,
                              B.BRANCH_ID,
                              COUNT(AD.LEAVE_ID) AS LEAVE_DAYS
                            FROM
                              HRIS_ATTENDANCE_DETAIL AD
                            JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=AD.EMPLOYEE_ID)
                            JOIN HRIS_BRANCHES B ON (B.BRANCH_ID=E.BRANCH_ID)
                            WHERE
                              AD.LEAVE_ID IS NOT NULL
                            {$dateCondition}
                               GROUP BY
                              B.BRANCH_ID,
                              AD.LEAVE_ID
                          )
                          PIVOT ( SUM ( LEAVE_DAYS )
                                                FOR leave_id
                                                IN ({$leaveIn}))) AA
                                                RIGHT JOIN HRIS_BRANCHES BB ON (AA.BRANCH_ID=BB.BRANCH_ID AND BB.STATUS='E')
    
                
                          
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedResult = Helper::extractDbData($result);
        return $this->convertLeaveIdToName($allLeave, $extractedResult, 'BRANCH_NAME');
    }

    public function filterLeaveReportDepartmnet($data) {
        $allLeave = $this->fetchAllLeave();
        $leaveIn = $this->leaveIn($allLeave);
        $fromCondition = "";
        $toCondition = "";


        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND AD.ATTENDANCE_DT >= {$fromDate->getExpression()}";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND AD.ATTENDANCE_DT <= {$toDate->getExpression()}";
        }
        $dateCondition = $fromCondition . $toCondition;
        $sql = <<<EOT
                                SELECT
                  AA.*,BB.DEPARTMENT_NAME
                FROM (SELECT
                  *
                FROM
                  (
                    SELECT
                      AD.LEAVE_ID,
                      D.DEPARTMENT_ID,
                      COUNT(AD.LEAVE_ID) AS LEAVE_DAYS
                    FROM
                      HRIS_ATTENDANCE_DETAIL AD
                    JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=AD.EMPLOYEE_ID)
                    JOIN HRIS_DEPARTMENTS D ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    WHERE
                      AD.LEAVE_ID IS NOT NULL
                        {$dateCondition}
                       GROUP BY
                      D.DEPARTMENT_ID,
                      AD.LEAVE_ID
                  )
                  PIVOT ( SUM ( LEAVE_DAYS )
                        FOR leave_id
                        IN ({$leaveIn}))) AA
                        RIGHT JOIN HRIS_DEPARTMENTS BB ON (AA.DEPARTMENT_ID=BB.DEPARTMENT_ID AND BB.STATUS='E')
    
                
                          
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedResult = Helper::extractDbData($result);
        return $this->convertLeaveIdToName($allLeave, $extractedResult, 'DEPARTMENT_NAME');
    }

    public function filterLeaveReportDesignation($data) {
        $allLeave = $this->fetchAllLeave();
        $leaveIn = $this->leaveIn($allLeave);
        $fromCondition = "";
        $toCondition = "";


        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND AD.ATTENDANCE_DT >= {$fromDate->getExpression()}";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND AD.ATTENDANCE_DT <= {$toDate->getExpression()}";
        }
        $dateCondition = $fromCondition . $toCondition;
        $sql = <<<EOT
                
                SELECT
  AA.*,BB.DESIGNATION_TITLE
FROM (SELECT
  *
FROM
  (
    SELECT
      AD.LEAVE_ID,
      D.DESIGNATION_ID,
      COUNT(AD.LEAVE_ID) AS LEAVE_DAYS
    FROM
      HRIS_ATTENDANCE_DETAIL AD
    JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=AD.EMPLOYEE_ID)
    JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=E.DESIGNATION_ID)
    WHERE
      AD.LEAVE_ID IS NOT NULL
                {$dateCondition}
       GROUP BY
      D.DESIGNATION_ID,
      AD.LEAVE_ID
  )
  PIVOT ( SUM ( LEAVE_DAYS )
                        FOR leave_id
                        IN ({$leaveIn}))) AA
                        RIGHT JOIN HRIS_DESIGNATIONS BB ON (AA.DESIGNATION_ID=BB.DESIGNATION_ID AND BB.STATUS='E')
    
        
        
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedResult = Helper::extractDbData($result);
        return $this->convertLeaveIdToName($allLeave, $extractedResult, 'DESIGNATION_TITLE');
    }

    public function filterLeaveReportPosition($data) {
        $allLeave = $this->fetchAllLeave();
        $leaveIn = $this->leaveIn($allLeave);
        $fromCondition = "";
        $toCondition = "";


        if (isset($data['fromDate']) && $data['fromDate'] != null && $data['fromDate'] != -1) {
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $fromCondition = "AND AD.ATTENDANCE_DT >= {$fromDate->getExpression()}";
        }
        if (isset($data['toDate']) && $data['toDate'] != null && $data['toDate'] != -1) {
            $toDate = Helper::getExpressionDate($data['toDate']);
            $toCondition = "AND AD.ATTENDANCE_DT <= {$toDate->getExpression()}";
        }
        $dateCondition = $fromCondition . $toCondition;
        $sql = <<<EOT
                
                SELECT
  AA.*,BB.POSITION_NAME
FROM (SELECT
  *
FROM
  (
    SELECT
      AD.LEAVE_ID,
      P.POSITION_ID,
      COUNT(AD.LEAVE_ID) AS LEAVE_DAYS
    FROM
      HRIS_ATTENDANCE_DETAIL AD
    JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=AD.EMPLOYEE_ID)
    JOIN HRIS_POSITIONS P ON (P.POSITION_ID=E.POSITION_ID)
    WHERE
      AD.LEAVE_ID IS NOT NULL
        {$dateCondition}
       GROUP BY
      P.POSITION_ID,
      AD.LEAVE_ID
  )
  PIVOT ( SUM ( LEAVE_DAYS )
                        FOR leave_id
                        IN ({$leaveIn}))) AA
                        RIGHT JOIN HRIS_POSITIONS BB ON (AA.POSITION_ID=BB.POSITION_ID AND BB.STATUS='E')
        
        
EOT;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedResult = Helper::extractDbData($result);
        return $this->convertLeaveIdToName($allLeave, $extractedResult, 'POSITION_NAME');
    }

    public function FetchNepaliMonth() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Months::class, NULL, [Months::FROM_DATE, Months::TO_DATE], NULL, NULL, NULL, 'M', true), false);
        $select->from(['M' => Months::TABLE_NAME])
                ->join(['FY' => FiscalYear::TABLE_NAME], 'FY.' . FiscalYear::FISCAL_YEAR_ID . '=M.' . Months::FISCAL_YEAR_ID, ["MONTH_NAME" => new Expression('CONCAT(FY.FISCAL_YEAR_NAME,M.MONTH_EDESC)')], "left");
        $select->where(["M.STATUS='E'", "FY.STATUS='E'", "TRUNC(SYSDATE)>M.FROM_DATE"]);
        $select->order("M." . Months::FROM_DATE . " DESC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    private function totalHiredEmployees($fromDate, $toDate) {
        $sql = "select count(*)as TOTAL from hris_employees 
            where JOIN_DATE BETWEEN " . $fromDate->getExpression() . " and " . $toDate->getExpression() . " and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function CalculateHireEmployees($data) {
        $returnArr = [];
        foreach ($data as $details) {
            $name = $details->name;
            $tempData = [
                'NAME' => $name
            ];
            $fromDate = Helper::getExpressionDate($details->fromDate);
            $toDate = Helper::getExpressionDate($details->toDate);
            $total = $this->totalHiredEmployees($fromDate, $toDate);
            $tempData['TOTAL'] = $total['TOTAL'];
            $sql = "select full_name,JOIN_DATE from hris_employees 
            where JOIN_DATE BETWEEN " . $fromDate->getExpression() . " and " . $toDate->getExpression() . " and status='E'";
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $tempData['DATA'] = Helper::extractDbData($result);
            array_push($returnArr, $tempData);
        }
        return $returnArr;
    }

    public function branchWiseDailyReport($monthId, $branchId) {

        $sql = <<<EOT
                      SELECT 
                      TRUNC(AD.ATTENDANCE_DT)-TRUNC(M.FROM_DATE)+1                              AS DAY_COUNT, 
                      E.EMPLOYEE_ID                                                             AS EMPLOYEE_ID ,
                      E.FIRST_NAME                                                                   AS FIRST_NAME,
                      E.MIDDLE_NAME                                                                  AS MIDDLE_NAME,
                      E.LAST_NAME                                                                    AS LAST_NAME,
                      CONCAT(CONCAT(CONCAT(E.FIRST_NAME,' '),CONCAT(E.MIDDLE_NAME, '')),E.LAST_NAME) AS FULL_NAME,
                      AD.ATTENDANCE_DT                                                               AS ATTENDANCE_DT,
                      (
                      CASE 
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL
                        AND AD.LEAVE_ID IS NOT NULL
                        THEN 1
                        ELSE 0
                      END) AS ON_LEAVE,
                      (
                      CASE
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.LEAVE_ID    IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NOT NULL
                        THEN 1
                        ELSE 0
                      END) AS IS_PRESENT,
                      (
                      CASE
                        WHEN AD.DAYOFF_FLAG ='N'
                        AND AD.LEAVE_ID   IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL
                        THEN 1
                        ELSE 0
                      END) AS IS_ABSENT,
                      (
                      CASE
                        WHEN AD.LEAVE_ID   IS NULL
                        AND AD.HOLIDAY_ID  IS NULL
                        AND AD.TRAINING_ID IS NULL
                        AND AD.TRAVEL_ID   IS NULL
                        AND AD.IN_TIME     IS NULL 
                          AND  AD.DAYOFF_FLAG='Y'
                        THEN 1
                        ELSE 0
                      END) AS IS_DAYOFF
                    FROM HRIS_ATTENDANCE_DETAIL AD
                    JOIN HRIS_EMPLOYEES E
                    ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID),
                      ( SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID=$monthId
                      ) M
                    WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
                    AND E.BRANCH_ID=$branchId
                    ORDER BY AD.ATTENDANCE_DT,
                      E.EMPLOYEE_ID
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function checkIfEmpowerTableExists() {
        return $this->checkIfTableExists('HR_MONTHLY_MODIFIED_PAY_VALUE');
    }

    public function toEmpower($fiscalYearId, $fiscalYearMonthNo) {
        $sql = "DECLARE
                  V_FISCAL_YEAR_ID       NUMBER:={$fiscalYearId};
                  V_FISCAL_YEAR_MONTH_NO NUMBER:={$fiscalYearMonthNo};
                BEGIN
                  DELETE
                  FROM HR_MONTHLY_MODIFIED_PAY_VALUE
                  WHERE PERIOD_DT_CODE=V_FISCAL_YEAR_MONTH_NO;
                  FOR report IN
                  (SELECT C.COMPANY_CODE,
                    C.COMPANY_CODE
                    ||'.01' AS BRANCH_CODE,
                    C.COMPANY_NAME,
                    D.DEPARTMENT_NAME,
                    A.EMPLOYEE_ID,
                    E.FULL_NAME,
                    A.DAYOFF,
                    A.PRESENT,
                    A.HOLIDAY,
                    A.LEAVE,
                    A.PAID_LEAVE,
                    A.UNPAID_LEAVE,
                    A.ABSENT,
                    A.DAYOFF              +A.PRESENT+A.HOLIDAY+A.UNPAID_LEAVE+A.PAID_LEAVE+A.ABSENT AS TOTAL_DAYS,
                    NVL(ROUND(OT.TOTAL_MIN/60,2),0)                                                 AS OVERTIME_HOUR,
                    A.TRAVEL,
                    A.TRAINING,
                    A.WORK_ON_HOLIDAY,
                    A.WORK_ON_DAYOFF
                  FROM
                    (SELECT A.EMPLOYEE_ID,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS IN( 'DO','WD')
                        THEN 1
                        ELSE 0
                      END) AS DAYOFF,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS IN ('PR','BA','LA','TV','VP','TN','TP')
                        THEN 1
                        ELSE 0
                      END) AS PRESENT,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS IN ('HD','WH')
                        THEN 1
                        ELSE 0
                      END) AS HOLIDAY,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS IN ('LV','LP')
                        THEN 1
                        ELSE 0
                      END) AS LEAVE,
                      SUM(
                      CASE
                        WHEN L.PAID = 'Y'
                        THEN 1
                        ELSE 0
                      END) AS PAID_LEAVE,
                      SUM(
                      CASE
                        WHEN L.PAID = 'N'
                        THEN 1
                        ELSE 0
                      END) AS UNPAID_LEAVE,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS = 'AB'
                        THEN 1
                        ELSE 0
                      END) AS ABSENT,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS= 'TV'
                        THEN 1
                        ELSE 0
                      END) AS TRAVEL,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS ='TN'
                        THEN 1
                        ELSE 0
                      END) AS TRAINING,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS = 'WH'
                        THEN 1
                        ELSE 0
                      END) WORK_ON_HOLIDAY,
                      SUM(
                      CASE
                        WHEN A.OVERALL_STATUS ='WD'
                        THEN 1
                        ELSE 0
                      END) WORK_ON_DAYOFF
                    FROM HRIS_ATTENDANCE_DETAIL A
                    LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                    ON (A.LEAVE_ID= L.LEAVE_ID)
                    LEFT JOIN
                      (SELECT *
                      FROM HRIS_MONTH_CODE
                      WHERE FISCAL_YEAR_ID    =3
                      AND FISCAL_YEAR_MONTH_NO=1
                      ) M
                    ON (A.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE)
                    GROUP BY A.EMPLOYEE_ID
                    ) A
                  LEFT JOIN HRIS_EMPLOYEES E
                  ON(A.EMPLOYEE_ID = E.EMPLOYEE_ID)
                  LEFT JOIN HRIS_COMPANY C
                  ON(E.COMPANY_ID= C.COMPANY_ID)
                  LEFT JOIN HRIS_DEPARTMENTS D
                  ON (E.DEPARTMENT_ID= D.DEPARTMENT_ID)
                  LEFT JOIN
                    (SELECT O.EMPLOYEE_ID,
                      SUM(O.TOTAL_HOUR) AS TOTAL_MIN
                    FROM HRIS_OVERTIME O
                    LEFT JOIN
                      (SELECT *
                      FROM HRIS_MONTH_CODE
                      WHERE FISCAL_YEAR_ID    =3
                      AND FISCAL_YEAR_MONTH_NO=1
                      ) M
                    ON (O.OVERTIME_DATE BETWEEN M.FROM_DATE AND M.TO_DATE)
                    WHERE O.STATUS= 'AP'
                    GROUP BY O.EMPLOYEE_ID
                    ) OT ON (A.EMPLOYEE_ID = OT.EMPLOYEE_ID)
                  ORDER BY C.COMPANY_NAME,
                    D.DEPARTMENT_NAME
                  )
                  LOOP
                    INSERT
                    INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                      (
                        PAY_CODE,
                        EMPLOYEE_CODE,
                        PERIOD_DT_CODE,
                        MODIFY_VALUE,
                        COMPANY_CODE,
                        BRANCH_CODE,
                        CREATED_BY,
                        CREATED_DATE,
                        SALARY_TYPE
                      )
                      VALUES
                      (
                        'TD',
                        report.EMPLOYEE_ID,
                        V_FISCAL_YEAR_MONTH_NO,
                        report.DAYOFF+report.PRESENT+report.HOLIDAY+report.UNPAID_LEAVE+report.PAID_LEAVE+report.ABSENT,
                        report.COMPANY_CODE,
                        report.BRANCH_CODE,
                        'SYSTEM',
                        SYSDATE,
                        0
                      );
                    --
                    INSERT
                    INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                      (
                        PAY_CODE,
                        EMPLOYEE_CODE,
                        PERIOD_DT_CODE,
                        MODIFY_VALUE,
                        COMPANY_CODE,
                        BRANCH_CODE,
                        CREATED_BY,
                        CREATED_DATE,
                        SALARY_TYPE
                      )
                      VALUES
                      (
                        'PD',
                        report.EMPLOYEE_ID,
                        V_FISCAL_YEAR_MONTH_NO,
                        report.PRESENT,
                        report.COMPANY_CODE,
                        report.BRANCH_CODE,
                        'SYSTEM',
                        SYSDATE,
                        0
                      );
                    INSERT
                    INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                      (
                        PAY_CODE,
                        EMPLOYEE_CODE,
                        PERIOD_DT_CODE,
                        MODIFY_VALUE,
                        COMPANY_CODE,
                        BRANCH_CODE,
                        CREATED_BY,
                        CREATED_DATE,
                        SALARY_TYPE
                      )
                      VALUES
                      (
                        'AD',
                        report.EMPLOYEE_ID,
                        V_FISCAL_YEAR_MONTH_NO,
                        report.ABSENT,
                        report.COMPANY_CODE,
                        report.BRANCH_CODE,
                        'SYSTEM',
                        SYSDATE,
                        0
                      );
                    INSERT
                    INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                      (
                        PAY_CODE,
                        EMPLOYEE_CODE,
                        PERIOD_DT_CODE,
                        MODIFY_VALUE,
                        COMPANY_CODE,
                        BRANCH_CODE,
                        CREATED_BY,
                        CREATED_DATE,
                        SALARY_TYPE
                      )
                      VALUES
                      (
                        'HD',
                        report.EMPLOYEE_ID,
                        V_FISCAL_YEAR_MONTH_NO,
                        report.HOLIDAY+report.DAYOFF,
                        report.COMPANY_CODE,
                        report.BRANCH_CODE,
                        'SYSTEM',
                        SYSDATE,
                        0
                      );
                    IF(report.PAID_LEAVE !=0) THEN
                      INSERT
                      INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                        (
                          PAY_CODE,
                          EMPLOYEE_CODE,
                          PERIOD_DT_CODE,
                          MODIFY_VALUE,
                          COMPANY_CODE,
                          BRANCH_CODE,
                          CREATED_BY,
                          CREATED_DATE,
                          SALARY_TYPE
                        )
                        VALUES
                        (
                          'PL',
                          report.EMPLOYEE_ID,
                          V_FISCAL_YEAR_MONTH_NO,
                          report.PAID_LEAVE,
                          report.COMPANY_CODE,
                          report.BRANCH_CODE,
                          'SYSTEM',
                          SYSDATE,
                          0
                        );
                    END IF;
                    IF(report.UNPAID_LEAVE !=0) THEN
                      INSERT
                      INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                        (
                          PAY_CODE,
                          EMPLOYEE_CODE,
                          PERIOD_DT_CODE,
                          MODIFY_VALUE,
                          COMPANY_CODE,
                          BRANCH_CODE,
                          CREATED_BY,
                          CREATED_DATE,
                          SALARY_TYPE
                        )
                        VALUES
                        (
                          'UL',
                          report.EMPLOYEE_ID,
                          V_FISCAL_YEAR_MONTH_NO,
                          report.UNPAID_LEAVE,
                          report.COMPANY_CODE,
                          report.BRANCH_CODE,
                          'SYSTEM',
                          SYSDATE,
                          0
                        );
                    END IF;
                    IF(report.OVERTIME_HOUR !=0) THEN
                      INSERT
                      INTO HR_MONTHLY_MODIFIED_PAY_VALUE
                        (
                          PAY_CODE,
                          EMPLOYEE_CODE,
                          PERIOD_DT_CODE,
                          MODIFY_VALUE,
                          COMPANY_CODE,
                          BRANCH_CODE,
                          CREATED_BY,
                          CREATED_DATE,
                          SALARY_TYPE
                        )
                        VALUES
                        (
                          'OT',
                          report.EMPLOYEE_ID,
                          V_FISCAL_YEAR_MONTH_NO,
                          report.OVERTIME_HOUR,
                          report.COMPANY_CODE,
                          report.BRANCH_CODE,
                          'SYSTEM',
                          SYSDATE,
                          0
                        );
                    END IF;
                  END LOOP;
                END;";
        $this->executeStatement($sql);
    }

}
