<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\AttendanceDetail;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AttendanceDetailRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AttendanceDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AttendanceDetail::ID => $id]);
    }

    public function editWith(Model $model, $where) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, $where);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
                    AttendanceDetail::ATTENDANCE_DT
                        ], [
                    AttendanceDetail::IN_TIME,
                    AttendanceDetail::OUT_TIME
                        ], NULL, NULL, 'A'), false);

//        $select->columns([
//            new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
//            new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),
//            new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
//            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
//            new Expression("A.ID AS ID"),
//            new Expression("A.IN_REMARKS AS IN_REMARKS"),
//            new Expression("A.OUT_REMARKS AS OUT_REMARKS")
//                ], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)'), "MIDDLE_NAME" => new Expression('INITCAP(E.MIDDLE_NAME)'), "LAST_NAME" => new Expression('INITCAP(E.LAST_NAME)')], "left");
        $select->where(["E.STATUS='E'"]);
        $select->where(["E.RETIRED_FLAG='N'"]);
        $select->order("E.FIRST_NAME,A.ATTENDANCE_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
//        print($statement->getSql());
//        exit;
        $result = $statement->execute();
        return $result;
    }

    //this function need changes
//    public function filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId = null, $employeeTypeId = null, $widOvertime = false, $onlyMisPunch = false) {
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//
//        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
//                    AttendanceDetail::ATTENDANCE_DT
//                        ], [
//                    AttendanceDetail::IN_TIME,
//                    AttendanceDetail::OUT_TIME
//                        ], NULL, NULL, 'A'), false);
//
//
//        $select->from(['A' => AttendanceDetail::TABLE_NAME])
//                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)'), "MIDDLE_NAME" => new Expression('INITCAP(E.MIDDLE_NAME)'), "LAST_NAME" => new Expression('INITCAP(E.LAST_NAME)')], "left")
//                ->join(['H' => 'HRIS_HOLIDAY_MASTER_SETUP'], 'A.HOLIDAY_ID=H.HOLIDAY_ID', ["HOLIDAY_ENAME" => 'HOLIDAY_ENAME'], "left")
//                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], 'A.LEAVE_ID=L.LEAVE_ID', ["LEAVE_ENAME" => 'LEAVE_ENAME'], "left")
//                ->join(['T' => 'HRIS_TRAINING_MASTER_SETUP'], 'A.TRAINING_ID=T.TRAINING_ID', ["TRAINING_NAME" => 'TRAINING_NAME'], "left")
//                ->join(['TVL' => 'HRIS_EMPLOYEE_TRAVEL_REQUEST'], 'A.TRAVEL_ID=TVL.TRAVEL_ID', ["TRAVEL_DESTINATION" => 'DESTINATION'], "left");
//
//        if ($widOvertime != false) {
//            $select->join(['OT' => 'HRIS_OVERTIME'], 'A.EMPLOYEE_ID = OT.EMPLOYEE_ID AND A.ATTENDANCE_DT=OT.OVERTIME_DATE', ["OVERTIME_ID" => 'OVERTIME_ID', 'OVERTIME_IN_HOUR' => new Expression("NVL2(OT.TOTAL_HOUR,LPAD(TRUNC(OT.TOTAL_HOUR/60,0),2, 0)||':'||LPAD(MOD(OT.TOTAL_HOUR,60),2, 0),NULL)")], "left");
//        }
//        if ($fromDate != null) {
//            $startDate = " AND A.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
//        } else {
//            $startDate = "";
//        }
//        if ($toDate != null) {
//            $endDate = " AND A.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
//        } else {
//            $endDate = "";
//        }
//        $select->where(["E.STATUS='E'" . $startDate . $endDate]);
//
//        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
//            $select->where(["E.RETIRED_FLAG='Y'"]);
//        } else {
//            $select->where(["E.RETIRED_FLAG='N'"]);
//        }
//        if ($status != "All") {
//            if ($status == "A") {
//                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NULL AND A.LEAVE_ID IS NULL AND A.DAYOFF_FLAG='N'"]);
//            }
//
//            if ($status == "H") {
//                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NOT NULL AND A.LEAVE_ID IS NULL"]);
//            }
//
//            if ($status == "L") {
//                $select->where(["A.IN_TIME IS NULL AND A.OUT_TIME IS NULL AND A.TRAINING_ID IS NULL AND A.HOLIDAY_ID IS NULL AND A.LEAVE_ID IS NOT NULL"]);
//            }
//
//            if ($status == "P") {
//                $select->where(["A.IN_TIME IS NOT NULL"]);
//            }
//            if ($status == "T") {
//                $select->where(["A.TRAINING_ID IS NOT NULL"]);
//            }
//            if ($status == "TVL") {
//                $select->where(["A.TRAVEL_ID IS NOT NULL"]);
//            }
//            if ($status == "WOH") {
//                $select->where(["(A.HOLIDAY_ID IS NOT NULL OR A.DAYOFF_FLAG = 'Y') AND A.IN_TIME IS NOT NULL AND A.OUT_TIME IS NOT NULL "]);
//            }
//            if ($status == "LI") {
//                $select->where(["(A.LATE_STATUS='L' OR A.LATE_STATUS='B')"]);
//            }
//            if ($status == "EO") {
//                $select->where(["(A.LATE_STATUS='E' OR A.LATE_STATUS='B')"]);
//            }
//        }
//
//        if ($employeeId != -1) {
//            $select->where(["E.EMPLOYEE_ID=" . $employeeId]);
//        }
//
//        if ($companyId != null && $companyId != -1) {
//            $select->where(["E.COMPANY_ID=" . $companyId]);
//        }
//        if ($employeeTypeId != null && $employeeTypeId != -1) {
//            $select->where(["E.EMPLOYEE_TYPE='" . $employeeTypeId . "'"]);
//        }
//
//        if ($branchId != -1) {
//            $select->where(["E.BRANCH_ID=" . $branchId]);
//        }
//
//        if ($departmentId != -1) {
//            $select->where(["E.DEPARTMENT_ID=" . $departmentId]);
//        }
//
//        if ($designationId != -1) {
//            $select->where(["E.DESIGNATION_ID=" . $designationId]);
//        }
//
//        if ($positionId != -1) {
//            $select->where(["E.POSITION_ID=" . $positionId]);
//        }
//
//        if ($serviceTypeId != -1) {
//            $select->where(["E.SERVICE_TYPE_ID=" . $serviceTypeId]);
//        }
//
//        if ($serviceEventTypeId != -1) {
//            $select->where(["E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId]);
//        }
//
//        if ($onlyMisPunch != false) {
//            $select->where([
//                "mod((SELECT COUNT(*) FROM HRIS_ATTENDANCE A1
//                WHERE A1.EMPLOYEE_ID = A.EMPLOYEE_ID
//                AND A1.ATTENDANCE_DT = A.ATTENDANCE_DT),2 )<>0"
//            ]);
//        }
//
//        $select->order("A.ATTENDANCE_DT DESC");
//        $select->order("A.IN_TIME");
//        $statement = $sql->prepareStatementForSqlObject($select);
//        print "<pre>";
//        print($statement->getSql());
//        exit;
//        $result = $statement->execute();
//        return $result;
//    }


    public function filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId = null, $employeeTypeId = null, $widOvertime = false, $missPunchOnly = false) {
        $fromDateCondition = "";
        $toDateCondition = "";
        $employeeCondition = '';
        $branchCondition = '';
        $departmentCondition = '';
        $positionCondition = '';
        $designationCondition = '';
        $serviceTypeCondition = '';
        $serviceEventtypeCondition = '';
        $statusCondition = '';
        $missPunchOnlyCondition = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND A.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY') ";
        }
        if ($toDate != null) {
            $toDateCondition = " AND A.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY') ";
        }
        if ($employeeId != null && $employeeId != -1) {
            $employeeCondition = " AND A.EMPLOYEE_ID ={$employeeId} ";
        }
        if ($branchId != null && $branchId != -1) {
            $branchCondition = " AND E.BRANCH_ID ={$branchId} ";
        }
        if ($departmentId != null && $departmentId != -1) {
            $departmentCondition = " AND E.DEPARTMENT_ID ={$departmentId} ";
        }
        if ($positionId != null && $positionId != -1) {
            $positionCondition = " AND E.POSITION_ID ={$positionId} ";
        }
        if ($designationId != null && $designationId != -1) {
            $designationCondition = " AND E.DESIGNATION_ID ={$designationId} ";
        }
        if ($serviceTypeId != null && $serviceTypeId != -1) {
            $serviceTypeCondition = " AND E.SERVICE_TYPE_ID ={$serviceTypeId} ";
        }
        if ($serviceEventTypeId != null && $serviceEventTypeId != -1) {
            $serviceEventtypeCondition = " AND E.SERVICE_EVENT_TYPE_ID ={$serviceEventTypeId} ";
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
                  INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT,
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
                  INITCAP(E.FULL_NAME)                             AS EMPLOYEE_NAME,
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
                {$branchCondition}
                {$departmentCondition}
                {$positionCondition}
                {$designationCondition}
                {$serviceTypeCondition}
                {$serviceEventtypeCondition}
                {$fromDateCondition}
                {$toDateCondition}
                {$statusCondition}
                {$missPunchOnlyCondition}
                ORDER BY A.ATTENDANCE_DT DESC
                ";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    public function filterRecordForMisPunch($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId) {
        
    }

    //may need changes
    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
                    AttendanceDetail::ATTENDANCE_DT
                        ], [
                    AttendanceDetail::IN_TIME,
                    AttendanceDetail::OUT_TIME
                        ], NULL, NULL, 'A'), false);
//        $select->columns([new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"), new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"), new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"), new Expression("A.IN_REMARKS AS IN_REMARKS"), new Expression("A.OUT_REMARKS AS OUT_REMARKS"), new Expression("A.TOTAL_HOUR AS TOTAL_HOUR")], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)')], "left");
        $select->where([AttendanceDetail::ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id) {
        
    }

    //no problem with changes
    public function getDtlWidEmpIdDate($employeeId, $attendanceDt) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
                    AttendanceDetail::ATTENDANCE_DT
                        ], [
                    AttendanceDetail::IN_TIME,
                    AttendanceDetail::OUT_TIME
                        ], NULL, NULL, 'A'), false);
//        $select->columns([
//            new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
//            new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),
//            new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
//            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
//            new Expression("A.ID AS ID"),
//            new Expression("A.IN_REMARKS AS IN_REMARKS"),
//            new Expression("A.OUT_REMARKS AS OUT_REMARKS")], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)'), "MIDDLE_NAME" => new Expression('INITCAP(E.MIDDLE_NAME)'), "LAST_NAME" => new Expression('INITCAP(E.LAST_NAME)')], "left");
        $select->where([
            'A.EMPLOYEE_ID=' . $employeeId,
            "A.ATTENDANCE_DT=TO_DATE('" . $attendanceDt . "','DD-MM-YYYY')"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    //ok
    public function addAttendance($model) {
        $attendanceTableGateway = new TableGateway(Attendance::TABLE_NAME, $this->adapter);
        return $attendanceTableGateway->insert($model->getArrayCopyForDB());
    }

    // no problem with changes    
    public function getNoOfDaysInDayInterval(int $employeeId, $startDate, $endDate, $includeHoliday = true) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
        $select->where(['A.' . AttendanceDetail::EMPLOYEE_ID . "=$employeeId"]);
        $select->where(['A.' . AttendanceDetail::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);

        if ($includeHoliday) {
            $select->where(['A.' . AttendanceDetail::HOLIDAY_ID . " IS NULL"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count();
    }

//    public function getNoOfDaysAbsent(int $employeeId, Expression $startDate, Expression $endDate) {
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
//        $select->where(['A.' . AttendanceDetail::EMPLOYEE_ID . "=$employeeId"]);
//        $select->where(['A.' . AttendanceDetail::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);
//        $select->where(['A.' . AttendanceDetail::LEAVE_ID . " IS NOT NULL"]);
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//
//        return $result->count();
//    }
    // need change with changes    
    public function getNoOfDaysAbsent(int $employeeId, Expression $startDate, Expression $endDate) {
        $startDt = $startDate->getExpression();
        $endDt = $endDate->getExpression();
        $sql = "SELECT SUM(LEAVE.LEAVE_COUNT) LEAVE_COUNT FROM (
                (SELECT COUNT(LR.EMPLOYEE_ID) AS LEAVE_COUNT FROM HRIS_EMPLOYEE_LEAVE_REQUEST LR,
                (SELECT  HAD.EMPLOYEE_ID, HAD.LEAVE_ID,HAD.ATTENDANCE_DT FROM HRIS_ATTENDANCE_DETAIL HAD
                WHERE HAD.EMPLOYEE_ID=$employeeId 
                AND (HAD.ATTENDANCE_DT BETWEEN 
                $startDt AND $endDt)
                AND HAD.LEAVE_ID IS NOT NULL
                ) AD
                WHERE
                LR.EMPLOYEE_ID = AD.EMPLOYEE_ID AND 
                LR.LEAVE_ID= AD.LEAVE_ID AND 
                LR.HALF_DAY = 'N') UNION (SELECT COUNT(LR.EMPLOYEE_ID)/2 AS LEAVE_COUNT FROM HRIS_EMPLOYEE_LEAVE_REQUEST LR,
                (SELECT  HAD.EMPLOYEE_ID, HAD.LEAVE_ID,HAD.ATTENDANCE_DT FROM HRIS_ATTENDANCE_DETAIL HAD
                WHERE HAD.EMPLOYEE_ID=7 
                AND (HAD.ATTENDANCE_DT BETWEEN 
                $startDt AND $endDt)
                AND HAD.LEAVE_ID IS NOT NULL
                ) AD
                WHERE
                LR.EMPLOYEE_ID = AD.EMPLOYEE_ID AND 
                LR.LEAVE_ID= AD.LEAVE_ID AND 
                LR.HALF_DAY != 'N') 
                ) LEAVE";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $extractedRes = Helper::extractDbData($result);
        if (sizeof($extractedRes) > 0) {
            return $extractedRes[0]['LEAVE_COUNT'];
        } else {
            return 0;
        }
    }

    // problem with changes    
    public function getNoOfDaysPresent(int $employeeId, Expression $startDate, Expression $endDate) {
        return $this->getNoOfDaysInDayInterval($employeeId, $startDate, $endDate) - $this->getNoOfDaysAbsent($employeeId, $startDate, $endDate);
    }

    // problem with changes | MODIFIED   
    public function getEmployeesAttendanceByDate($date, bool $presentFlag, $branchId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([Helper::timeExpression(AttendanceDetail::IN_TIME, 'A'), new Expression("A.ID AS ID")], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
        $select->join(['E' => HrEmployees::TABLE_NAME], "A." . AttendanceDetail::EMPLOYEE_ID . "=" . "E." . HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME]);
        $select->where(['A.' . AttendanceDetail::LEAVE_ID . " IS NULL", 'A.' . AttendanceDetail::HOLIDAY_ID . " IS NULL", 'A.' . AttendanceDetail::TRAINING_ID . " IS NULL"]);

        $select->where(['A.' . AttendanceDetail::ATTENDANCE_DT . " = " . $date->getExpression()]);

        $select->where(['A.' . AttendanceDetail::DAYOFF_FLAG . " = 'N'"]);
        if ($presentFlag) {
            $select->where(['A.' . AttendanceDetail::IN_TIME . " IS NOT NULL"]);
        } else {
            $select->where(['A.' . AttendanceDetail::IN_TIME . " IS NULL"]);
        }

        if ($branchId != null) {
            $select->where(["E." . AttendanceDetail::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

    //may need change
    public function getleaveIdCount(int $employeeId, Expression $startDate, Expression $endDate) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
        $select->columns([Helper::columnExpression(AttendanceDetail::LEAVE_ID, "A", "COUNT", AttendanceDetail::LEAVE_ID . "_NO"), AttendanceDetail::LEAVE_ID], true);
        $select->where(['A.' . AttendanceDetail::EMPLOYEE_ID . "=$employeeId"]);
        $select->where(['A.' . AttendanceDetail::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);
        $select->where(['A.' . AttendanceDetail::LEAVE_ID . " IS NOT NULL"]);
        $select->group(['A.' . AttendanceDetail::LEAVE_ID]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    //need change
    public function getTotalNoOfWorkingDays(Expression $startDate, Expression $endDate) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => AttendanceDetail::TABLE_NAME]);
        $select->columns([Helper::columnExpression(AttendanceDetail::ATTENDANCE_DT, "DISTINCT  A", null, null)]);
        $select->where(['A.' . AttendanceDetail::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);
//        $select->where(['A.' . AttendanceDetail::HOLIDAY_ID . " IS NULL"]);
//        $select->group(['A.' . AttendanceDetail::ATTENDANCE_DT]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count();
    }

    public function checkAndUpdateLeaves(Expression $date) {
        
    }

    //may need changes
    public function fetchByEmpIdAttendanceDT($employeeId, $attendanceDt) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceDetail::class, NULL, [
                    AttendanceDetail::ATTENDANCE_DT
                        ], [
                    AttendanceDetail::IN_TIME,
                    AttendanceDetail::OUT_TIME
                        ], NULL, NULL, 'A'), false);
//        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"), new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"), new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"), new Expression("A.IN_REMARKS AS IN_REMARKS"), new Expression("A.OUT_REMARKS AS OUT_REMARKS")], true);
        $select->from(['A' => AttendanceDetail::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression('INITCAP(E.FIRST_NAME)'), "MIDDLE_NAME" => new Expression('INITCAP(E.MIDDLE_NAME)'), "LAST_NAME" => new Expression('INITCAP(E.LAST_NAME)')], "left");
        $select->where([
            'A.EMPLOYEE_ID=' . $employeeId,
            "A.ATTENDANCE_DT=" . $attendanceDt
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEmployeeShfitDetails($employeeId) {
        $sql = "SELECT INITCAP(TO_CHAR(SYSDATE, 'HH:MI AM')) AS CURRENT_TIME,
            INITCAP(TO_CHAR(S.START_TIME+((S.LATE_IN*60)/86400), 'HH:MI AM')) AS CHECKIN_TIME, 
            INITCAP(TO_CHAR(S.END_TIME-((S.EARLY_OUT*60)/86400), 'HH:MI AM')) AS CHECKOUT_TIME,
            ESA.*,S.* FROM HRIS_EMPLOYEES E
                join HRIS_EMPLOYEE_SHIFT_ASSIGN ESA on (ESA.EMPLOYEE_ID=E.EMPLOYEE_ID)
                JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=ESA.SHIFT_ID)
                WHERE E.EMPLOYEE_ID=$employeeId AND ESA.STATUS='E' AND ESA.MODIFIED_DT IS NULL
                AND (TO_DATE(TRUNC(SYSDATE), 'DD-MON-YY') BETWEEN TO_DATE(S.START_DATE, 'DD-MON-YY') AND TO_DATE(S.END_DATE, 'DD-MON-YY'))";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEmployeeDefaultShift() {
        $sql = "SELECT INITCAP(TO_CHAR(SYSDATE, 'HH:MI AM')) AS CURRENT_TIME,
      INITCAP(TO_CHAR(S.START_TIME+((S.LATE_IN*60)/86400), 'HH:MI AM'))   AS CHECKIN_TIME,
      INITCAP(TO_CHAR(S.END_TIME-((S.EARLY_OUT*60)/86400), 'HH:MI AM')) AS CHECKOUT_TIME,S.*
            FROM HRIS_SHIFTS S
            WHERE S.DEFAULT_SHIFT='Y'
            AND (TO_DATE(TRUNC(SYSDATE), 'DD-MON-YY') BETWEEN TO_DATE(S.START_DATE, 'DD-MON-YY') AND TO_DATE(S.END_DATE, 'DD-MON-YY'))";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
