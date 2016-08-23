<?php
namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class BranchRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_BRANCHES',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['CREATED_DT']);
        unset($array['BRANCH_ID']);
        $this->tableGateway->update($array,["BRANCH_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }


    public function fetchById($id)
    {
           $rowset= $this->tableGateway->select(['BRANCH_ID'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->delete(['BRANCH_ID'=>$id]);
    }
}