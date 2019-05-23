<?php

namespace Cafeteria\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class CafeteriaReports implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        //$this->tableGateway = new TableGateway(CafeteriaMenuModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        //$this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        //$this->tableGateway->update([CafeteriaMenuModel::STATUS => 'D'], [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function edit(Model $model, $id) {
        //$this->tableGateway->update($model->getArrayCopyForDB(), [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function fetchEmployeeWiseDetails(){
        
    }
}
