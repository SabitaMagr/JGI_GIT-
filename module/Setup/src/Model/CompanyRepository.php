<?php
namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CompanyRepository implements RepositoryInterface
{
    private $tableGateway;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_COMPANY',$adapter);

    }

    public function add($model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDb());
    }

    public function edit($model,$id,$modifiedDt)
    {
        $array = $model->getArrayCopyForDb();
        $newArray = array_merge($array,['MODIFIED_DT'=>$modifiedDt]);
        $this->tableGateway->update($newArray,["COMPANY_ID"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['COMPANY_ID'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
    	$this->tableGateway->delete(['COMPANY_ID'=>$id]);

    }
}