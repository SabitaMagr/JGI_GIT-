<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAttendanceModel;
use Customer\Model\CustContractEmp;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
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
        $this->gateway->delete([CustContractEmp::CONTRACT_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = "select CONTRACT_ID,EMPLOYEE_ID,OLD_EMPLOYEE_ID,
            TO_CHAR(START_DATE, 'DD/MM/YYYY') AS START_DATE, 
            TO_CHAR(END_DATE, 'DD/MM/YYYY') AS END_DATE, 
            TO_CHAR(ASSIGNED_DATE, 'DD/MM/YYYY') AS ASSIGNED_DATE 
            from HRIS_CUST_CONTRACT_EMP WHERE 
                CONTRACT_ID=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function updateEmployeeAttendance(Model $model, $updateArray) {
        $tempArray = $model->getArrayCopyForDB();

        $custmomerAttendanceGateway = new TableGateway(ContractAttendanceModel::TABLE_NAME, $this->adapter);
        $custmomerAttendanceGateway->update($tempArray, $updateArray);
    }

    public function getAllMonthBetweenTwoDates($startDate, $endDate) {
        $sql = "select MONTH_ID,MONTH_EDESC,FROM_DATE,TO_DATE,YEAR,MONTH_NO,(YEAR||' '||MONTH_EDESC) AS MONTH_TITLE from hris_month_code where 
                month_id between 
                (select month_id from hris_month_code where '{$startDate}' between from_date and to_date)
                and 
                (
                select month_id from (select  month_id from hris_month_code where '{$endDate}' between from_date and to_date
                union ALL
                select max(month_id) from hris_month_code) where rownum=1
                )";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getAllMonthWiseEmployees($contractId,$monthId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustContractEmp::class, NULL, NULL, [
                    CustContractEmp::START_TIME,
                    CustContractEmp::END_TIME
                        ], null, null, null, false, false, [
                    CustContractEmp::WORKING_HOUR,
                ]), false);
        $select->from(CustContractEmp::TABLE_NAME);
        $select->where([CustContractEmp::MONTH_CODE_ID => $monthId,CustContractEmp::CONTRACT_ID => $contractId]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    
    
    public function deleteContractEmpMonthly($id,$monthId) {
        $this->gateway->delete([CustContractEmp::CONTRACT_ID => $id,CustContractEmp::MONTH_CODE_ID => $monthId]);
    }

}
