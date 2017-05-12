<?php

namespace HolidayManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Exception;
use HolidayManagement\Model\EmployeeHoliday;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Model\HolidayBranch;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class HolidayRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [
            Holiday::HOLIDAY_ID => $id
        ]);
    }

    public function fetchAll($today = null) {
        $sql = "
            SELECT  
            INITCAP(TO_CHAR(A.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
            INITCAP(TO_CHAR(A.END_DATE, 'DD-MON-YYYY')) AS END_DATE,
            A.HOLIDAY_ID,
            A.HOLIDAY_CODE,
            INITCAP(A.HOLIDAY_ENAME) AS HOLIDAY_ENAME,
            INITCAP(A.HOLIDAY_LNAME) AS HOLIDAY_LNAME,
            A.HALFDAY
                FROM HRIS_HOLIDAY_MASTER_SETUP A 
                WHERE A.STATUS='E'";
        if ($today != null) {
            $sql .= " AND (" . $today->getExpression() . " between A.START_DATE AND A.END_DATE) OR " . $today->getExpression() . " <= A.START_DATE";
        }
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("HOLIDAY_CODE AS HOLIDAY_CODE"),
            new Expression("INITCAP(HOLIDAY_ENAME) AS HOLIDAY_ENAME"),
            new Expression("INITCAP(TO_CHAR(START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("INITCAP(HOLIDAY_LNAME) AS HOLIDAY_LNAME"),
            new Expression("STATUS AS STATUS"),
            new Expression("HALFDAY AS HALFDAY"),
            new Expression("REMARKS AS REMARKS"),
                ], true);

        $select->from(Holiday::TABLE_NAME);

        $select->where([
            Holiday::HOLIDAY_ID => $id,
            Holiday::STATUS => 'E'
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Holiday::STATUS => 'D'], [Holiday::HOLIDAY_ID => $id]);
    }

    public function filterRecords($fromDate, $toDate) {
        $sql = "
SELECT INITCAP(TO_CHAR(A.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
  INITCAP(TO_CHAR(A.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
  A.HOLIDAY_ID,
  A.HOLIDAY_CODE,
  INITCAP(A.HOLIDAY_ENAME) AS HOLIDAY_ENAME,
  INITCAP(A.HOLIDAY_LNAME) AS HOLIDAY_LNAME,
  CASE
    WHEN A.HALFDAY = 'F'
    THEN 'First Half'
    ELSE
      CASE
        WHEN A.HALFDAY='S'
        THEN 'Second Half'
        ELSE 'Full Day'
      END
  END AS HALFDAY
FROM HRIS_HOLIDAY_MASTER_SETUP A
WHERE A.STATUS ='E'
";

        if ($fromDate != null) {
            $sql .= " AND A.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= " AND A.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        $sql .= " ORDER BY A.START_DATE";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function filter($branchId, $genderId, Expression $date) {
        throw new Exception("HolidayRepository => filter is changed. plz use ");
    }

    public function filterHoliday($employeeId, Expression $afterDate = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(["H" => Holiday::TABLE_NAME]);

        if ($employeeId != null) {
            $select->join(["EH" => EmployeeHoliday::TABLE_NAME], "H." . Holiday::HOLIDAY_ID . " = EH." . EmployeeHoliday::HOLIDAY_ID);
            $select->where(["EH." . EmployeeHoliday::EMPLOYEE_ID . "= $employeeId"]);
        }

        if ($afterDate != null) {
            $select->where(["H." . Holiday::START_DATE . ">= " . $afterDate->getExpression()]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

}
