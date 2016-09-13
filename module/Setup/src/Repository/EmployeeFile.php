<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class EmployeeFile implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_EMPLOYEE_FILE',$adapter);

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['CREATED_DT']);
        unset($array['FILE_CODE']);
        $this->tableGateway->update($array,["FILE_CODE"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }


    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select(['EMPLOYEE_ID'=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->delete(['FILE_CODE'=>$id]);
    }
}