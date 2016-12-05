<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/4/16
 * Time: 10:28 AM
 */

namespace Payroll\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\PayPositionSetup;
use Payroll\Model\Rules;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class PayPositionRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(PayPositionSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        return $this->gateway->select([PayPositionSetup::PAY_ID => $id]);
    }

    public function fetchByPositionId($id) {
        return $this->gateway->select([PayPositionSetup::POSITION_ID => $id]);
    }

    public function delete($id) {
        return $this->gateway->delete([PayPositionSetup::PAY_ID => $id[0], PayPositionSetup::POSITION_ID => $id[1]]);
    }

    public function test($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['HPS' => Rules::TABLE_NAME]);
        $select->join(['HPPS' => PayPositionSetup::TABLE_NAME], "HPS." . Rules::PAY_ID . " =HPPS." . PayPositionSetup::PAY_ID, []);
        $select->order("HPS.".Rules::PRIORITY_INDEX . " " . \Zend\Db\Sql\Select::ORDER_ASCENDING);
        $select->where("HPPS." . PayPositionSetup::POSITION_ID . ' = ' . $id);
        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

}
