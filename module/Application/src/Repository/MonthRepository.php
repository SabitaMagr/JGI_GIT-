<?php

namespace Application\Repository;

use Application\Model\Model;
use Application\Model\Months;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class MonthRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Months::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        return $this->gateway->select([Months::FISCAL_YEAR_ID => $id, Months::STATUS => 'E']);
    }

}
