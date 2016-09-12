<?php

namespace HolidayManagement\Repository;

use Setup\Model\Model;
use Setup\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class HolidayRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_HOLIDAY_MASTER_SETUP',$adapter);
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