<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustContractWeekdays;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CustContractWeekdaysRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustContractWeekdays::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->delete([CustContractWeekdays::CONTRACT_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = "select * from(select * from HRIS_CUST_CONTRACT_WEEKDAYS where contract_id={$id})
PIVOT 
(
  count(WEEKDAY)
  FOR WEEKDAY
  IN (1 AS SUN,2 AS MON,3 AS TUE,4 AS WED,5 AS THU,6 AS FRI,7 AS SAT)
)";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($rawResult);
    }

}
