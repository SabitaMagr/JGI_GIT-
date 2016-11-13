<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 3:38 PM
 */

namespace AttendanceManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\AttendanceByHr;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AttendanceByHrRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AttendanceByHr::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AttendanceByHr::ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"), new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"), new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"), new Expression("A.IN_REMARKS AS IN_REMARKS"), new Expression("A.OUT_REMARKS AS OUT_REMARKS")], true);
        $select->from(['A' => AttendanceByHr::TABLE_NAME])
                ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME', "MIDDLE_NAME" => 'MIDDLE_NAME', "LAST_NAME" => 'LAST_NAME']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"), new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"), new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"), new Expression("A.IN_REMARKS AS IN_REMARKS"), new Expression("A.OUT_REMARKS AS OUT_REMARKS"), new Expression("A.TOTAL_HOUR AS TOTAL_HOUR")], true);
        $select->from(['A' => AttendanceByHr::TABLE_NAME])
                ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME']);
        $select->where([AttendanceByHr::ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id) {
        
    }

    public function getDtlWidEmpIdDate($employeeId, $attendanceDt) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"), new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"), new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"), new Expression("A.IN_REMARKS AS IN_REMARKS"), new Expression("A.OUT_REMARKS AS OUT_REMARKS")], true);
        $select->from(['A' => AttendanceByHr::TABLE_NAME])
                ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME', "MIDDLE_NAME" => 'MIDDLE_NAME', "LAST_NAME" => 'LAST_NAME']);
        $select->where([
            'A.EMPLOYEE_ID=' . $employeeId,
            "A.ATTENDANCE_DT>=TO_DATE('" . $attendanceDt . "','DD-MM-YYYY')"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function addAttendance($model) {
        $attendanceTableGateway = new TableGateway(Attendance::TABLE_NAME, $this->adapter);
        return $attendanceTableGateway->insert($model->getArrayCopyForDB());
    }

    public function getNoOfDaysInDayInterval(int $employeeId, $startDate, $endDate) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => AttendanceByHr::TABLE_NAME]);
        $select->where(['A.' . AttendanceByHr::EMPLOYEE_ID . "=$employeeId"]);
        $select->where(['A.' . AttendanceByHr::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);
        $select->where(['A.' . AttendanceByHr::HOLIDAY_ID . " IS NULL"]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count();
    }

    public function getNoOfDaysAbsent(int $employeeId, Expression $startDate, Expression $endDate) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => AttendanceByHr::TABLE_NAME]);
        $select->where(['A.' . AttendanceByHr::EMPLOYEE_ID . "=$employeeId"]);
        $select->where(['A.' . AttendanceByHr::ATTENDANCE_DT . " BETWEEN " . $startDate->getExpression() . " AND " . $endDate->getExpression()]);
        $select->where(['A.' . AttendanceByHr::LEAVE_ID . " IS NOT NULL"]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count();
    }

    public function getNoOfDaysPresent(int $employeeId, Expression $startDate, Expression $endDate) {
        return $this->getNoOfDaysInDayInterval($employeeId, $startDate, $endDate) - $this->getNoOfDaysAbsent($employeeId, $startDate, $endDate);
    }

}
