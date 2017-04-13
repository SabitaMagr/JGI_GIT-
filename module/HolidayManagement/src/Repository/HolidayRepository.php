<?php

namespace HolidayManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Model\HolidayBranch;
use Setup\Model\Branch;
use Setup\Model\Designation;
use Setup\Model\HolidayDesignation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class HolidayRepository implements RepositoryInterface {

    private $tableGateway;
    private $tableGatewayHolidayBranch;
    private $tableGatewayHolidayDesignation;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayBranch = new TableGateway(HolidayBranch::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayDesignation = new TableGateway(HolidayDesignation::TABLE_NAME, $adapter);
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

        $sql = "SELECT  TO_CHAR(A.START_DATE, 'DD-MON-YYYY') AS START_DATE,TO_CHAR(A.END_DATE, 'DD-MON-YYYY') AS END_DATE,A.HOLIDAY_ID,A.HOLIDAY_CODE,A.HOLIDAY_ENAME,A.HOLIDAY_LNAME,B.GENDER_NAME,A.HALFDAY
                FROM HRIS_HOLIDAY_MASTER_SETUP A 
                LEFT OUTER JOIN HRIS_GENDERS B 
                ON A.GENDER_ID=B.GENDER_ID
                WHERE A.STATUS='E'";
        if ($today != null) {
            $sql .= " AND (" . $today->getExpression() . " between A.START_DATE AND A.END_DATE) OR " . $today->getExpression() . " <= A.START_DATE";
        }
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function checkEmployeeOnHoliday($date, $branchId, $genderId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['H' => Holiday::TABLE_NAME]);
        $select->where(["H." . Holiday::GENDER_ID . "=$genderId" . " OR " . "H." . Holiday::GENDER_ID . " IS NULL"]);
        $select->where([$date->getExpression() . " BETWEEN " . "H." . Holiday::START_DATE . " AND H." . Holiday::END_DATE]);
        $select->where(["H." . Holiday::BRANCH_ID . " IS NULL"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("HOLIDAY_CODE AS HOLIDAY_CODE"),
            new Expression("HOLIDAY_ENAME AS HOLIDAY_ENAME"),
            new Expression("INITCAP(TO_CHAR(START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("HOLIDAY_LNAME AS HOLIDAY_LNAME"),
            new Expression("GENDER_ID AS GENDER_ID"),
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

    public function addHolidayBranch(Model $model) {
        $this->tableGatewayHolidayBranch->insert($model->getArrayCopyForDB());
    }

    public function addHolidayDesignation(HolidayDesignation $model) {
        $this->tableGatewayHolidayDesignation->insert($model->getArrayCopyForDB());
    }

    public function deleteHolidayBranch($holidayId, $branchId) {
        $this->tableGatewayHolidayBranch->delete([
            HolidayBranch::HOLIDAY_ID => $holidayId,
            HolidayBranch::BRANCH_ID => $branchId
        ]);
    }

    public function deleteHolidayDesignation($holidayId, $designationId) {
        $this->tableGatewayHolidayDesignation->delete([
            HolidayDesignation::HOLIDAY_ID => $holidayId,
            HolidayDesignation::DESIGNATION_ID => $designationId
        ]);
    }

    public function selectHolidayBranch($holidayId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HB' => HolidayBranch::TABLE_NAME])
                ->join(['B' => "HRIS_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["HB.HOLIDAY_ID" => $holidayId]);
        $select->where(["B.STATUS" => 'E']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset;
    }

    public function selectHolidayDesignation($holidayId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HD' => HolidayDesignation::TABLE_NAME])
                ->join(['D' => Designation::TABLE_NAME], 'HD.' . HolidayDesignation::DESIGNATION_ID . '=D.' . Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE]);

        $select->where(["HD.HOLIDAY_ID" => $holidayId]);
        $select->where(["D.STATUS" => 'E']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset;
    }

    public function selectHolidayBranchWidHidBid($holidayId, $branchId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HB' => HolidayBranch::TABLE_NAME])
                ->join(['B' => "HRIS_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["HB.HOLIDAY_ID" => $holidayId]);
        $select->where(["HB.BRANCH_ID" => $branchId]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset;
    }

    public function filterRecords($fromDate, $toDate, $branchId, $genderId) {
        $branchName = "";
        $joinQuery = "";
        if ($branchId != -1) {
            $branchName = ",D.BRANCH_NAME";
            $joinQuery = "INNER JOIN HRIS_HOLIDAY_BRANCH C
                            ON A.HOLIDAY_ID=C.HOLIDAY_ID 
                            INNER JOIN HRIS_BRANCHES D
                            ON C.BRANCH_ID=D.BRANCH_ID";
        }

        $sql = "SELECT TO_CHAR(A.START_DATE, 'DD-MON-YYYY') AS START_DATE,TO_CHAR(A.END_DATE, 'DD-MON-YYYY') AS END_DATE, A.HOLIDAY_ID,A.HOLIDAY_CODE,A.HOLIDAY_ENAME,A.HOLIDAY_LNAME,B.GENDER_NAME,A.HALFDAY
                " . $branchName . " FROM HRIS_HOLIDAY_MASTER_SETUP A 
                LEFT OUTER JOIN HRIS_GENDERS B 
                ON A.GENDER_ID=B.GENDER_ID " . $joinQuery . " WHERE A.STATUS ='E'";

        if ($fromDate != null) {
            $sql .= " AND A.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= " AND A.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

//        if ($genderId == null) {
//            $sql .= " AND B.GENDER_NAME is null";
//        } else 
        if ($genderId != null) {
            $sql .= " AND B.GENDER_ID=" . $genderId;
        }

        if ($branchId != -1) {
            $sql .= " AND C.BRANCH_ID=" . $branchId;
        }
        $sql .= " ORDER BY A.START_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function filter($branchId, $genderId, Expression $date) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(["H" => Holiday::TABLE_NAME]);

        if ($branchId != null) {
            $select->join(["HB" => HolidayBranch::TABLE_NAME], "H." . Holiday::HOLIDAY_ID . " = HB." . HolidayBranch::HOLIDAY_ID);
            $select->where(["HB." . HolidayBranch::BRANCH_ID . "= $branchId"]);
        }

        if ($genderId != null) {
            $select->where(["(H." . Holiday::GENDER_ID . "= $genderId OR " . "H." . Holiday::GENDER_ID . " IS NULL)"]);
        }

        if ($date != null) {
            $select->where(["H." . Holiday::START_DATE . ">= " . $date->getExpression()]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

    public function selectHolidayOfEmployee(int $empId, Expression $startDate = null, Expression $endDate = null) {
//       "NO_OF_HOLIDAYS_MONTHLY"
//        
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//
//        $select->columns([]);
//        $select->from(['E' => HrEmployees::TABLE_NAME])
//                ->join(['HB' => HolidayBranch::TABLE_NAME], 'E.' . HrEmployees::BRANCH_ID . ' = ' . 'HB.' . HolidayBranch::BRANCH_ID, [])
//                ->join(['H' => Holiday::TABLE_NAME], 'HB.' . HolidayBranch::HOLIDAY_ID . ' = ' . 'H.' . Holiday::HOLIDAY_ID);
//
//        $select->where(["E." . HrEmployees::EMPLOYEE_ID . ' = ' . $empId]);
//        $select->where(["( " . "H." . Holiday::GENDER_ID . ' = ' . "E." . HrEmployees::GENDER_ID . " OR " . "H." . Holiday::GENDER_ID . ' IS NULL ' . ")"]);
//        $select->where(['( H.' . Holiday::END_DATE . " >= " . $startDate->getExpression() . " OR " . 'H.' . Holiday::START_DATE . " <= " . $endDate->getExpression()." )"]);
//        $statement = $sql->prepareStatementForSqlObject($select);
//        print "<pre>";
//        print($statement->getSql());
//        exit;
    }

}
