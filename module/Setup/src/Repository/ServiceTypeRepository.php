<?php

namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class ServiceTypeRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_SERVICE_TYPES',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array=$model->getArrayCopyForDB();
        unset($array['SERVICE_TYPE_ID']);
        unset($array['CREATED_DT']);
        $this->tableGateway->update( $array,["SERVICE_TYPE_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['SERVICE_TYPE_ID'=>$id]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select(['STATUS'=>'E']);       
    }

    public function delete($id)
    {
    	$this->tableGateway->delete(['SERVICE_TYPE_ID'=>$id]);

    }
}