<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\LoanPermission;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class LoanPermissionRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanPermission::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[LoanPermission::PERMISSION_ID]);
        unset($array[LoanPermission::CREATED_DATE]);
        $this->tableGateway->update($array, [LoanPermission::PERMISSION_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([LoanPermission::STATUS => 'D'], [LoanPermission::PERMISSION_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select([LoanPermission::PERMISSION_ID=>$id]);
	return $row->current();
    }

}
