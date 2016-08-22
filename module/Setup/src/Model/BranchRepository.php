<?php
namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class BranchRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_BRANCHES',$adapter);

    }

    public function add($model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDb());
    }

    public function edit($model,$id,$modifiedDt)
    {
        $array = $model->getArrayCopyForDb();
        $newArray = array_merge($array,['MODIFIED_DT'=>$modifiedDt]);
        $this->tableGateway->update($newArray,["BRANCH_ID"=>$id]);
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