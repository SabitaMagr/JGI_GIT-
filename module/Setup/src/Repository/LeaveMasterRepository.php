<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class LeaveMasterRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_LEAVE_MASTER_SETUP',$adapter);
        $this->adapter=$adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['LEAVE_ID']);
        unset($array['CREATED_DT']);
        unset($array['STATUS']);
        $this->tableGateway->update($array,["LEAVE_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(['STATUS'=>'E']);
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//        $select->from("HR_LEAVE_MASTER_SETUP");
////        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Shift(), ['startTime','endTime']), false);
//        $select->where(['STATUS'=>'E']);
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//        return $result;
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['LEAVE_ID'=>$id,'STATUS'=>'E']);
        return $rowset->current();
    }

    public function fetchActiveRecord()
    {
        return  $rowset= $this->tableGateway->select(['STATUS'=>'E']);
    }

    public function delete($id)
    {
//    	$this->tableGateway->delete(['SHIFT_ID'=>$id]);
        $this->tableGateway->update(['STATUS'=>'D'],['LEAVE_ID'=>$id]);

    }
}