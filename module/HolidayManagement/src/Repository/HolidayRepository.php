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

class HolidayRepository implements RepositoryInterface
{
    private $tableGateway;
    private $tableGatewayHolidayBranch;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayBranch = new TableGateway(HolidayBranch::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function addHolidayBranch(Model $model)
    {
        $this->tableGatewayHolidayBranch->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [
            Holiday::HOLIDAY_ID => $id
        ]);
    }

    public function fetchAll()
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(H.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(H.END_DATE, 'DD-MON-YYYY') AS END_DATE"),
            new Expression("H.HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("H.HOLIDAY_CODE AS HOLIDAY_CODE"),
            new Expression("H.HOLIDAY_ENAME AS HOLIDAY_ENAME"),
            new Expression("H.HALFDAY AS HALFDAY"),
            new Expression("H.FISCAL_YEAR AS FISCAL_YEAR"),
            new Expression("H.REMARKS AS REMARKS"),
            ], true);
        $select->from(['H' => Holiday::TABLE_NAME])
            ->join(['G' => 'HR_GENDERS'], 'H.GENDER_ID=G.GENDER_ID', ['GENDER_NAME']);

        $select->where(["H.STATUS='E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select([
            Holiday::HOLIDAY_ID => $id,
            Holiday::STATUS => 'E'
        ]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([
            Holiday::STATUS => 'D'
        ], [
            Holiday::HOLIDAY_ID => $id
        ]);

    }
    public function filterRecords($holidayId,$branchId,$genderId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(H.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(H.END_DATE, 'DD-MON-YYYY') AS END_DATE"),
            new Expression("H.HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("H.HOLIDAY_CODE AS HOLIDAY_CODE"),
            new Expression("H.HOLIDAY_ENAME AS HOLIDAY_ENAME"),
            new Expression("H.HALFDAY AS HALFDAY"),
            new Expression("H.FISCAL_YEAR AS FISCAL_YEAR"),
            new Expression("H.REMARKS AS REMARKS"),
        ], true);
        $select->from(['H' => Holiday::TABLE_NAME])
            ->join(['G' => 'HR_GENDERS'], 'H.GENDER_ID=G.GENDER_ID', ['GENDER_NAME'])
            ->join(['HB'=> 'HR_HOLIDAY_BRANCH'],'H.HOLIDAY_ID=HB.HOLIDAY_ID',['BRANCH_ID'])
            ->join(['B' => "HR_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["H.STATUS='E'"]);

        if ($holidayId != -1) {
            $select->where(["H.HOLIDAY_ID=$holidayId"]);
        }

        if ($branchId != -1) {
            $select->where(["HB.BRANCH_ID=$branchId"]);
        }

        if ($genderId != -1) {
            $select->where(["H.GENDER_ID= $genderId"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}