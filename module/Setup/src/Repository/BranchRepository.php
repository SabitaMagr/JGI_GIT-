<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class BranchRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Branch::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Branch::BRANCH_ID => $id]);
    }

    public function fetchAll() {        
        return $this->tableGateway->select([Branch::STATUS => 'E']);
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Branch::BRANCH_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Branch::STATUS => 'D'], [Branch::BRANCH_ID => $id]);
    }
}
