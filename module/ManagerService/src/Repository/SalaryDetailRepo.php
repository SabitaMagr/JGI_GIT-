<?php

namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use ManagerService\Model\SalaryDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class SalaryDetailRepo implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(SalaryDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchById($id) {
        return $this->tableGateway->select($id)->current();
    }

    public function fetchIfAvailable(Expression $fromDate, Expression $toDate, int $employeeId) {
        $sql = "SELECT TO_CHAR(SD.EFFECTIVE_DATE,'DD-MON-YYYY') AS EFFECTIVE_DATE,SD.OLD_AMOUNT
            
                FROM HRIS_SALARY_DETAIL SD
                WHERE (SD.EFFECTIVE_DATE BETWEEN " . $fromDate->getExpression() . " AND " . $toDate->getExpression() . " )
                AND SD.EMPLOYEE_ID=$employeeId
                AND SD.STATUS='E' ORDER BY SD.EFFECTIVE_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
