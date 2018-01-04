<?php

namespace Customer\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustContractDates;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CustContractDatesRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustContractDates::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql="select CONTRACT_ID,TO_CHAR(MANUAL_DATE, 'DD/MM/YYYY') AS MANUAL_DATE from HRIS_CUST_CONTRACT_DATES WHERE CONTRACT_ID=$id";
        $statement = $this->adapter->query($sql);
        $result= $statement->execute();
        return Helper::extractDbData($result);
    }

}
