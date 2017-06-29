<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 3:38 PM
 */

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

    public function recordFilter($fromDate, $toDate, $employeeId, $status,$onlyMisPunch) {

//        $sql="SELECT * FROM HRIS_ATTENDANCE_DETAIL
//            WHERE EMPLOYEE_ID=".$employeeId." AND ATTENDANCE_DT>='".$fromDate."' AND ATTENDANCE_DT<='".$toDate."'";
//        $statement = $this->adapter->query($sql);
//        $result = $statement->execute();
//        return $result;
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
            $select->where([
                "mod((SELECT COUNT(*) FROM HRIS_ATTENDANCE A1
                WHERE A1.EMPLOYEE_ID = A.EMPLOYEE_ID
                AND A1.ATTENDANCE_DT = A.ATTENDANCE_DT),2 )<>0"
            ]);
        }


        $select->order("A.ATTENDANCE_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
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
