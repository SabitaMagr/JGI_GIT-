<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\ServiceType;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class ServiceTypeRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(ServiceType::TABLE_NAME,$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array=$model->getArrayCopyForDB();
        unset($array[ServiceType::SERVICE_TYPE_ID]);
        unset($array[ServiceType::CREATED_DT]);
        $this->tableGateway->update( $array,[ServiceType::SERVICE_TYPE_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([ServiceType::SERVICE_TYPE_ID=>$id]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select(function(Select $select){
             $select->where([ServiceType::STATUS=>'E']);
             $select->order(ServiceType::SERVICE_TYPE_NAME." ASC");
         });
    }

    public function delete($id)
    {
    	$this->tableGateway->update([ServiceType::STATUS=>'D'],[ServiceType::SERVICE_TYPE_ID=>$id]);
    }
}