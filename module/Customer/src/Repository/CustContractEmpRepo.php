<?php

namespace Customer\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustContractEmp;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CustContractEmpRepo implements RepositoryInterface {
    
    private $adapter;
    private $gateway;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustContractEmp::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->delete([CustContractEmp::CONTRACT_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql="select CONTRACT_ID,EMPLOYEE_ID,OLD_EMPLOYEE_ID,
            TO_CHAR(START_DATE, 'DD/MM/YYYY') AS START_DATE, 
            TO_CHAR(END_DATE, 'DD/MM/YYYY') AS END_DATE, 
            TO_CHAR(ASSIGNED_DATE, 'DD/MM/YYYY') AS ASSIGNED_DATE 
            from HRIS_CUST_CONTRACT_EMP WHERE 
                CONTRACT_ID=$id";
        $statement = $this->adapter->query($sql);
        $result= $statement->execute();
        return Helper::extractDbData($result);
        
    }

}
