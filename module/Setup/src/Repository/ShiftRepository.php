<?php

namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Shift;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ShiftRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Shift::TABLE_NAME,$adapter);
        $this->adapter=$adapter;
    }

     public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array[Shift::SHIFT_ID]);
        unset($array[Shift::CREATED_DT]);
        unset($array[Shift::STATUS]);
        $this->tableGateway->update($array,[Shift::SHIFT_ID=>$id]);
    }

    public function fetchAll()
    {
//        return $this->tableGateway->select();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(Shift::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Shift(), ['startDate','endDate'],['startTime','endTime']),false);
        $select->where([Shift::STATUS=>'E']);
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(Shift::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Shift(), ['startDate','endDate'],['startTime','endTime','halfTime','halfDayEndTime']),false);
        $select->where([Shift::SHIFT_ID=>$id]);
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result->current();

    }
    public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select([Shift::STATUS=>'E']);
    }

    public function delete($id)
    {
        $this->tableGateway->update([Shift::STATUS=>'D'],[Shift::SHIFT_ID=>$id]);

    }
}