<?php
namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class BranchRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('branch',$adapter);

    }

    public function add(ModelInterface $model)
    {
        $this->tableGateway->insert($model->getArrayCopy());
    }

    public function edit(ModelInterface $model,$id)
    {
        $this->tableGateway->update($model->getArrayCopy(),["branchCode"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
           $rowset= $this->tableGateway->select(['branchCode'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->delete(['branchCode'=>$id]);
    }
}