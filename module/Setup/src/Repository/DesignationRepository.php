<?php

namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_DESIGNATIONS', $adapter);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select(["DESIGNATION_ID" => $id]);
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['DESIGNATION_ID']);
        unset($array['CREATED_DT']);
        $this->tableGateway->update($array, ["DESIGNATION_ID" => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(["DESIGNATION_ID" => $id]);
    }

}