<?php

namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class JobHistoryRepository implements RepositoryInterface
{
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_JOB_HISTORY', $adapter);
    }

    public function add(Model $model)
    {
        var_dump($model->getArrayCopyForDB());die();
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['JOB_HISTORY_ID']);
        $this->tableGateway->update($array, ["JOB_HISTORY_ID" => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(["JOB_HISTORY_ID" => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $row = $this->tableGateway->select(["JOB_HISTORY_ID" => $id]);
        return $row->current();
    }
}