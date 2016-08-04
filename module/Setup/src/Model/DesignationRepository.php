<?php

namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('designation',$adapter);
    }

    public function fetchAll()
    {
       return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
       $rowset= $this->tableGateway->select(["designationCode"=>$id]);
        return $rowset->current();
    }

    public function add(ModelInterface $model)
    {
        $this->tableGateway->insert($model->getArrayCopy());
    }

    public function edit(ModelInterface $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopy(),["designationCode"=>$id]);
    }

    public function delete($id)
    {
    }

}