<?php

namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class ServiceTypeRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_SERVICE_TYPES',$adapter);

    }

    public function add($model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit($model,$id,$modifiedDt)
    {
        $array=$model->getArrayCopyForDB();
        $newArray =  array_merge($array, ['MODIFIED_DT'=> $modifiedDt ]);
        $this->tableGateway->update( $newArray,["SERVICE_TYPE_ID"=>$id]);
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