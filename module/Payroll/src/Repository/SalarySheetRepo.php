<?php

namespace Payroll\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\SalarySheet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class SalarySheetRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(SalarySheet::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        return $this->gateway->select();
    }

    public function fetchById($id) {
        return $this->gateway->select([SalarySheet::SHEET_NO => $id]);
    }

    public function fetchByIds(array $ids) {
        return $this->gateway->select($ids);
    }

}
