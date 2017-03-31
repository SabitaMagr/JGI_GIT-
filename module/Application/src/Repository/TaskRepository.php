<?php

namespace Application\Repository;

use Application\Model\Model;
use Application\Model\TaskModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class TaskRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(TaskModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
         $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

}
