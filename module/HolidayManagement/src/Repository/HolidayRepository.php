<?php

namespace HolidayManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use HolidayManagement\Model\Holiday;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use HolidayManagement\Model\HolidayBranch;

class HolidayRepository implements RepositoryInterface {

    private $tableGateway;
    private $tableGatewayHolidayBranch;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayBranch = new TableGateway(HolidayBranch::TABLE_NAME, $adapter);
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

    public function fetchAll($today=null) {

        $sql = "SELECT A.START_DATE,A.END_DATE,A.HOLIDAY_ID,A.HOLIDAY_CODE,A.HOLIDAY_ENAME,A.HOLIDAY_LNAME,B.GENDER_NAME,A.HALFDAY
                FROM HR_HOLIDAY_MASTER_SETUP A 
                LEFT OUTER JOIN HR_GENDERS B 
                ON A.GENDER_ID=B.GENDER_ID
                WHERE A.STATUS='E'";
        if($today!=null){
            $sql .= " AND (".$today->getExpression()." between A.START_DATE AND A.END_DATE) OR ".$today->getExpression()." <= A.START_DATE";
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
        $rowset = $this->tableGateway->select([
            Holiday::HOLIDAY_ID => $id,
            Holiday::STATUS => 'E'
        ]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([
            Holiday::STATUS => 'D'
                ], [
            Holiday::HOLIDAY_ID => $id
        ]);
    }

    public function addHolidayBranch(Model $model) {
        $this->tableGatewayHolidayBranch->insert($model->getArrayCopyForDB());
    }

    public function deleteHolidayBranch($holidayId, $branchId) {
        $this->tableGatewayHolidayBranch->delete([
            HolidayBranch::HOLIDAY_ID => $holidayId,
            HolidayBranch::BRANCH_ID => $branchId
        ]);
    }

    public function selectHolidayBranch($holidayId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HB' => HolidayBranch::TABLE_NAME])
                ->join(['B' => "HR_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["HB.HOLIDAY_ID" => $holidayId]);
        $select->where(["B.STATUS" => 'E']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset;
    }

    public function selectHolidayBranchWidHidBid($holidayId, $branchId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HB' => HolidayBranch::TABLE_NAME])
                ->join(['B' => "HR_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

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
            $joinQuery = "INNER JOIN HR_HOLIDAY_BRANCH C
ON A.HOLIDAY_ID=C.HOLIDAY_ID 
INNER JOIN HR_BRANCHES D
ON C.BRANCH_ID=D.BRANCH_ID";
        }

        $sql = "SELECT A.START_DATE,A.END_DATE, A.HOLIDAY_ID,A.HOLIDAY_CODE,A.HOLIDAY_ENAME,A.HOLIDAY_LNAME,B.GENDER_NAME,A.HALFDAY
" . $branchName . " FROM HR_HOLIDAY_MASTER_SETUP A 
LEFT OUTER JOIN HR_GENDERS B 
ON A.GENDER_ID=B.GENDER_ID " . $joinQuery . " WHERE A.STATUS ='E'";

        if ($fromDate != null) {
            $sql .= " AND A.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($fromDate != null) {
            $sql .= " AND A.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($genderId == null) {
            $sql .= " AND B.GENDER_NAME is null";
        } else if ($genderId != null) {
            $sql .= " AND B.GENDER_ID=" . $genderId;
        }

        if ($branchId != -1) {
            $sql .= " AND C.BRANCH_ID=" . $branchId;
        }

        $statement = $this->adapter->query($sql);
        // return $statement->getSql();
        $result = $statement->execute();
        return $result;
    }

}
