<?php

namespace Payroll\Repository;

use Application\Model\Model;
use Payroll\Model\PayEmployeeSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class PayEmployeeRepo {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(PayEmployeeSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function fetchByPayId($id) {
        return $this->gateway->select([PayEmployeeSetup::PAY_ID => $id]);
    }

    public function fetchByEmployeeId($id) {
        $sql = "
                SELECT P.PAY_ID,
                  P.PAY_EDESC,
                  P.PAY_TYPE_FLAG,
                  P.PRIORITY_INDEX,
                  PD.IS_MONTHLY,
                  PD.MNENONIC_NAME
                FROM HRIS_PAY_SETUP P
                JOIN HRIS_PAY_DETAIL_SETUP PD
                ON (P.PAY_ID  =PD.PAY_ID)
                JOIN HRIS_PAY_EMPLOYEE_SETUP PE
                ON (PE.PAY_ID=P.PAY_ID)
                WHERE PE.EMPLOYEE_ID={$id}";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function deleteByPayId($id) {
        return $this->gateway->delete([PayEmployeeSetup::PAY_ID => $id]);
    }

    public function deleteByEmployeeId($id) {
        return $this->gateway->delete([PayEmployeeSetup::EMPLOYEE_ID => $id]);
    }

}
