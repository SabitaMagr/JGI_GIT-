<?php
namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CompanyRepository implements RepositoryInterface
{
    private $tableGateway;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('company',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopy());
    }

    public function edit(Model $model, $id, $modifiedDt)
    {
        $this->tableGateway->update($model->getArrayCopy(),["companyCode"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['companyCode'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
    	$this->tableGateway->delete(['companyCode'=>$id]);

    }
}