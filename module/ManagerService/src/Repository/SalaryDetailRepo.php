<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use ManagerService\Model\SalaryDetail;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
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
        return $this->tableGateway->select([SalaryDetail::STATUS=>'E']);
    }
    
    
    public function fetchActiveRecord() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(SalaryDetail::class, NULL,NULL,NULL,NULL,NULL,'SD',FALSE,FALSE), false);
        $select->from(['SD' => SalaryDetail::TABLE_NAME]);
        $select->join(['E' => HrEmployees::TABLE_NAME], "E.". HrEmployees::EMPLOYEE_ID."=SD.".SalaryDetail::EMPLOYEE_ID, [HrEmployees::FIRST_NAME => new Expression('INITCAP(E.FIRST_NAME)'),HrEmployees::MIDDLE_NAME => new Expression('INITCAP(E.MIDDLE_NAME)'),HrEmployees::LAST_NAME => new Expression('INITCAP(E.LAST_NAME)')], 'left');
        $select->where(["SD.".SalaryDetail::STATUS."='E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        return $rowset;
    }


//    public function fetchById($id) {
//        return $this->tableGateway->select([SalaryDetail::SALARY_DETAIL_ID => $id])->current();
//    }

    public function fetchById($id) {
     $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['SD' => SalaryDetail::TABLE_NAME]);
        $select->where(["SD." . SalaryDetail::SALARY_DETAIL_ID . "='".$id."'"]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new SalaryDetail(), [
                    'effectiveDate',
                        ], NULL, 'SD'), false);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
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
