<?php

namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\AttendanceDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AttendanceRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AttendanceDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchByEmpId($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"),
            new Expression("INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM')) AS IN_TIME"),
            new Expression("INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM')) AS OUT_TIME"),
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("A.ID AS ID"),
            new Expression("A.IN_REMARKS AS IN_REMARKS"),
            new Expression("A.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR")
                ], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)")], "left");
        $select->where(['A.EMPLOYEE_ID' => $id]);
        $select->order("A.ATTENDANCE_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function recordFilter($fromDate, $toDate, $employeeId, $status, $onlyMisPunch) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
                    AttendanceDetail::ATTENDANCE_DT
                        ], [
                    AttendanceDetail::IN_TIME,
                    AttendanceDetail::OUT_TIME
                        ], NULL, NULL, 'A'), false);

        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)'), "MIDDLE_NAME" => new Expression('INITCAP(E.MIDDLE_NAME)'), "LAST_NAME" => new Expression('INITCAP(E.LAST_NAME)')], "left")
                ->join(['H' => 'HRIS_HOLIDAY_MASTER_SETUP'], 'A.HOLIDAY_ID=H.HOLIDAY_ID', ["HOLIDAY_ENAME" => 'HOLIDAY_ENAME'], "left")
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], 'A.LEAVE_ID=L.LEAVE_ID', ["LEAVE_ENAME" => 'LEAVE_ENAME'], "left")
                ->join(['T' => 'HRIS_TRAINING_MASTER_SETUP'], 'A.TRAINING_ID=T.TRAINING_ID', ["TRAINING_NAME" => 'TRAINING_NAME'], "left")
                ->join(['TVL' => 'HRIS_EMPLOYEE_TRAVEL_REQUEST'], 'A.TRAVEL_ID=TVL.TRAVEL_ID', ["TRAVEL_DESTINATION" => 'DESTINATION'], "left");

        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
        if ($fromDate != null) {
            $startDate = " AND A.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        } else {
            $startDate = "";
        }
        if ($toDate != null) {
            $endDate = " AND A.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        } else {
            $endDate = "";
        }
        $select->where([
            'A.EMPLOYEE_ID=' . $employeeId .
            $startDate . $endDate
        ]);

        if ($status != "All") {
            if ($status == "A") {
                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NULL AND A.LEAVE_ID IS NULL AND A.DAYOFF_FLAG='N'"]);
            }

            if ($status == "H") {
                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NOT NULL AND A.LEAVE_ID IS NULL"]);
            }

            if ($status == "L") {
                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NULL AND A.LEAVE_ID IS NOT NULL"]);
            }

            if ($status == "P") {
                $select->where(["A.IN_TIME IS NOT NULL"]);
            }
            if ($status == "T") {
                $select->where(["A.TRAINING_ID IS NOT NULL"]);
            }
            if ($status == "TVL") {
                $select->where(["A.TRAVEL_ID IS NOT NULL"]);
            }
            if ($status == "WOH") {
                $select->where(["A.HOLIDAY_ID IS NOT NULL AND A.IN_TIME IS NOT NULL "]);
            }
            if ($status == "LI") {
                $select->where(["(A.LATE_STATUS='L' OR A.LATE_STATUS='B')"]);
            }
            if ($status == "EO") {
                $select->where(["(A.LATE_STATUS='E' OR A.LATE_STATUS='B')"]);
            }
            if ($status == "WODO") {
                $select->where(["A.DAYOFF_FLAG = 'Y' AND A.IN_TIME IS NOT NULL "]);
            }
        }

        if ($onlyMisPunch != false) {
            $select->where(["(A.LATE_STATUS='X' OR A.LATE_STATUS='Y')"]);
        }


        $select->order("A.ATTENDANCE_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function attendanceReport($fromDate, $toDate, $employeeId, $status, $missPunchOnly = false) {
        $fromDateCondition = "";
        $toDateCondition = "";
        $employeeCondition = '';
        $statusCondition = '';
        $missPunchOnlyCondition = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND A.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY') ";
        }
        if ($toDate != null) {
            $toDateCondition = " AND A.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY') ";
        }
        if ($employeeId != null) {
            $employeeCondition = " AND A.EMPLOYEE_ID ={$employeeId} ";
        }
        if ($status == "A") {
            $statusCondition = "AND A.OVERALL_STATUS = 'AB'";
        }

        if ($status == "H") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'HD' OR A.OVERALL_STATUS = 'WH' ) ";
        }

        if ($status == "L") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'LV' OR A.OVERALL_STATUS = 'LP' ) ";
        }

        if ($status == "P") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'PR' OR A.OVERALL_STATUS = 'WD' OR A.OVERALL_STATUS = 'WH' OR A.OVERALL_STATUS = 'BA' OR A.OVERALL_STATUS = 'LA' OR A.OVERALL_STATUS = 'TP' OR A.OVERALL_STATUS = 'LP' OR A.OVERALL_STATUS = 'VP' ) ";
        }
        if ($status == "T") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'TN' OR A.OVERALL_STATUS = 'TP' ) ";
        }
        if ($status == "TVL") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'TV' OR A.OVERALL_STATUS = 'VP' ) ";
        }
        if ($status == "WOH") {
            $statusCondition = "AND A.OVERALL_STATUS = 'WH'";
        }
        if ($status == "WOD") {
            $statusCondition = "AND A.OVERALL_STATUS = 'WD'";
        }
        if ($status == "LI") {
            $statusCondition = "AND (A.LATE_STATUS = 'L' OR A.LATE_STATUS = 'B' OR A.LATE_STATUS ='Y') ";
        }
        if ($status == "EO") {
            $statusCondition = "AND (A.LATE_STATUS = 'E' OR A.LATE_STATUS = 'B' ) ";
        }

        if ($missPunchOnly) {
            $missPunchOnlyCondition = "AND (A.LATE_STATUS = 'X' OR A.LATE_STATUS = 'Y' ) ";
        }

        $sql = "
                SELECT A.ID                                        AS ID,
                  A.EMPLOYEE_ID                                    AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT_AD,
                  BS_DATE(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT_BS,
                  INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM'))          AS IN_TIME,
                  INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM'))         AS OUT_TIME,
                  A.IN_REMARKS                                     AS IN_REMARKS,
                  A.OUT_REMARKS                                    AS OUT_REMARKS,
                  MIN_TO_HOUR(A.TOTAL_HOUR)                        AS TOTAL_HOUR,
                  A.LEAVE_ID                                       AS LEAVE_ID,
                  A.HOLIDAY_ID                                     AS HOLIDAY_ID,
                  A.TRAINING_ID                                    AS TRAINING_ID,
                  A.TRAVEL_ID                                      AS TRAVEL_ID,
                  A.SHIFT_ID                                       AS SHIFT_ID,
                  A.DAYOFF_FLAG                                    AS DAYOFF_FLAG,
                  A.LATE_STATUS                                    AS LATE_STATUS,
                  INITCAP(E.FIRST_NAME)                            AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME)                           AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)                             AS LAST_NAME,
                  H.HOLIDAY_ENAME                                  AS HOLIDAY_ENAME,
                  L.LEAVE_ENAME                                    AS LEAVE_ENAME,
                  T.TRAINING_NAME                                  AS TRAINING_NAME,
                  TVL.DESTINATION                                  AS TRAVEL_DESTINATION,
                  (
                  CASE
                    WHEN A.OVERALL_STATUS = 'DO'
                    THEN 'Day Off'
                    WHEN A.OVERALL_STATUS ='HD'
                    THEN 'On Holiday('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LV'
                    THEN 'On Leave('
                      ||L.LEAVE_ENAME
                      || ')'
                    WHEN A.OVERALL_STATUS ='TV'
                    THEN 'On Travel('
                      ||TVL.DESTINATION
                      ||')'
                    WHEN A.OVERALL_STATUS ='TN'
                    THEN 'On Training('
                      ||T.TRAINING_NAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='WD'
                    THEN 'Work On Dayoff'
                    WHEN A.OVERALL_STATUS ='WH'
                    THEN 'Work on Holiday('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LP'
                    THEN 'Work on Leave('
                      ||L.LEAVE_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='VP'
                    THEN 'Work on Travel('
                      ||TVL.DESTINATION
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='TP'
                    THEN 'Present('
                      ||T.TRAINING_NAME
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='PR'
                    THEN 'Present'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='AB'
                    THEN 'Absent'
                    WHEN A.OVERALL_STATUS ='BA'
                    THEN 'Present(Late In and Early Out)'
                    WHEN A.OVERALL_STATUS ='LA'
                    THEN 'Present(Third Day Late)'
                  END)AS STATUS
                FROM HRIS_ATTENDANCE_DETAIL A
                LEFT JOIN HRIS_EMPLOYEES E
                ON A.EMPLOYEE_ID=E.EMPLOYEE_ID
                LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP H
                ON A.HOLIDAY_ID=H.HOLIDAY_ID
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON A.LEAVE_ID=L.LEAVE_ID
                LEFT JOIN HRIS_TRAINING_MASTER_SETUP T
                ON A.TRAINING_ID=T.TRAINING_ID
                LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST TVL
                ON A.TRAVEL_ID      =TVL.TRAVEL_ID
                WHERE 1=1
                {$employeeCondition}
                {$fromDateCondition}
                {$toDateCondition}
                {$statusCondition}
                {$missPunchOnlyCondition}
                ORDER BY A.ATTENDANCE_DT DESC
                ";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    public function delete($id) {
        
    }

    public function getCurrentNeplaiMonthStartDateEndDate() {
        $sql = "SELECT FISCAL_YEAR_ID,
                  MONTH_ID,
                  MONTH_EDESC,
                  INITCAP((TO_CHAR(FROM_DATE,'DD-MON-YYYY'))) AS FROM_DATE,
                  INITCAP((TO_CHAR(TO_DATE,'DD-MON-YYYY')))   AS TO_DATE
                FROM HRIS_MONTH_CODE
                WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE AND TO_DATE";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
