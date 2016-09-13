<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DepartmentRepository implements RepositoryInterface
{
    private $tableGateway;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_DEPARTMENTS',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $temp=$model->getArrayCopyForDB();
        unset($temp['DEPARTMENT_ID']);
        unset($temp['CREATED_DT']);
        $this->tableGateway->update($temp,["DEPARTMENT_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['DEPARTMENT_ID'=>$id]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select(['STATUS'=>'E']);
        
    }


    public function delete($id)
    {
    	$this->tableGateway->delete(['DEPARTMENT_ID'=>$id]);

    }
}