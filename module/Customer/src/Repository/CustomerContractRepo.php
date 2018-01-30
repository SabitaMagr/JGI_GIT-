<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustomerContract;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CustomerContractRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustomerContract::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContract::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        $sql = "SELECT CC.CONTRACT_ID AS CONTRACT_ID,CC.CONTRACT_NAME AS CONTRACT_NAME,
                  C.CUSTOMER_ENAME,
                  TO_CHAR(CC.START_DATE,'DD-MON-YYYY') AS START_DATE_AD,
                  BS_DATE(CC.START_DATE)               AS START_DATE_BS,
                  TO_CHAR(CC.END_DATE,'DD-MON-YYYY')   AS END_DATE_AD,
                  BS_DATE(CC.END_DATE)                 AS END_DATE_BS,
                  WORKING_CYCLE_DESC(CC.WORKING_CYCLE) AS WORKING_CYCLE,
                  CHARGE_TYPE_DESC(CC.CHARGE_TYPE)     AS CHARGE_TYPE,
                  CC.CHARGE_RATE,
                  CC.REMARKS
                FROM HRIS_CUSTOMER_CONTRACT CC
                JOIN HRIS_CUSTOMER C
                ON (CC.CUSTOMER_ID=C.CUSTOMER_ID)
                WHERE CC.STATUS      ='E'
                ORDER BY C.CUSTOMER_ENAME";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult;
    }

    public function fetchById($id) {
        $rawResult = $this->gateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerContract::class,NULL, [
                        CustomerContract::START_DATE,
                        CustomerContract::END_DATE,
                    ]
                    ), false);
            $select->where([CustomerContract::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->where([CustomerContract::CONTRACT_ID => $id]);
        });
        return $rawResult->current();
    }

}
