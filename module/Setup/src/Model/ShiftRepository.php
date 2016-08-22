<?php

namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class ShiftRepository implements RepositoryInterface
{
    private $tableGateway;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_SHIFTS',$adapter);
    }

     public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDb());
    }


    public function edit(Model $model,$id,$modifiedDt)
    {
        $array = $model->getArrayCopyForDb();
        $newArray = array_merge($array,["MODIFIED_DT"=>$modifiedDt]);
        $this->tableGateway->update($newArray,["SHIFT_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['SHIFT_ID'=>$id]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select(['STATUS'=>'E']);       
    }

    public function delete($id)
    {
    	$this->tableGateway->delete(['SHIFT_ID'=>$id]);

    }
}