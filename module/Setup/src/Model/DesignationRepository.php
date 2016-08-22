<?php

namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_DESIGNATIONS',$adapter);
    }

    public function fetchAll()
    {
       return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
       $rowset= $this->tableGateway->select(["DESIGNATION_ID"=>$id]);
        return $rowset->current();
    }

    public function add($model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDb());
    }

    public function edit($model, $id,$modifiedDt)
    {
        $array = $model->getArrayCopyForDb();
        $newArray = array_merge($array,['MODIFIED_DT'=>$modifiedDt]);
        $this->tableGateway->update($newArray,["DESIGNATION_ID"=>$id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(["DESIGNATION_ID"=>$id]);
    }

}