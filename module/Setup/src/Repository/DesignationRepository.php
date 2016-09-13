<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Designation::TABLE_NAME, $adapter);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([Designation::STATUS=>'E']);
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