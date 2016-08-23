<?php
namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CompanyRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_COMPANY',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['COMPANY_ID']);
        unset($array['CREATED_DT']);
        $this->tableGateway->update($array,["COMPANY_ID"=>$id]);
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