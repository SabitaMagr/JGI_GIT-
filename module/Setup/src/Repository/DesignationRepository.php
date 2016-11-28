<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Designation::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        
        $select->from(["D1" => Designation::TABLE_NAME])
                ->join(["D2" => Designation::TABLE_NAME],'D1.PARENT_DESIGNATION=D2.DESIGNATION_ID',["PARENT_DESIGNATION_TITLE"=>"DESIGNATION_TITLE"]);
        $select->where(["D1.STATUS= 'E'"]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
        //print_r($statement->getSql()); die();
        ///return $this->tableGateway->select([Designation::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select([Designation::DESIGNATION_ID => $id,Designation::STATUS=>'E']);
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Designation::DESIGNATION_ID => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->update([Designation::STATUS=>'D'],["DESIGNATION_ID" => $id]);
    }

}