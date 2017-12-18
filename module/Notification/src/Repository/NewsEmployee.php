<?php

namespace Notification\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Notification\Model\NewsEmployeeModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class NewsEmployee implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(NewsEmployeeModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
//        PRINT_R($model->getArrayCopyForDB());
//        DIE();
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
