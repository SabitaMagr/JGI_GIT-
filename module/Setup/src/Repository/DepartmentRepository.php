<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class DepartmentRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(Department::TABLE_NAME,$adapter);
        $this->adapter =  $adapter;

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[Department::DEPARTMENT_ID=>$id]);
    }

    public function fetchAll()
    {
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['D'=>Department::TABLE_NAME]);
        $select->join(['C' => "HR_COUNTRIES"], "D." . Department::COUNTRY_ID . "=C.COUNTRY_ID", ['COUNTRY_NAME'], 'left')
               ->join(['PD' => Department::TABLE_NAME], "D." . Department::PARENT_DEPARTMENT . "=PD.DEPARTMENT_ID", ['PARENT_DEPARTMENT'=>'DEPARTMENT_NAME'], 'left');
        $select->where(["D.STATUS='E'"]);
        $select->order("D.".Department::DEPARTMENT_NAME." ASC");        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([Department::DEPARTMENT_ID=>$id]);
        return $result->current();
    }

    public function delete($id)
    {
    	$this->tableGateway->update([Department::STATUS=>'D'],[Department::DEPARTMENT_ID=>$id]);
    }
}