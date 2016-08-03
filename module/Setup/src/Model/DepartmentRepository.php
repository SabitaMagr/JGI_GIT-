<?php

namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DepartmentRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('department',$adapter);

    }

    public function add(ModelInterface $model)
    {
        $this->tableGateway->insert($model->getArrayCopy());
    }

    public function edit(ModelInterface $model,$id)
    {
        $this->tableGateway->update($model->getArrayCopy(),["departmentCode"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['departmentCode'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
    	$this->tableGateway->delete(['departmentCode'=>$id]);

    }
}