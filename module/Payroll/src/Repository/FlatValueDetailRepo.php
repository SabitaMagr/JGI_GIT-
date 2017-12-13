<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValueDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class FlatValueDetailRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(FlatValueDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function delete($id) {
        
    }

    public function fetchById($id) {
        $sql = "
                SELECT F.FLAT_VALUE
                FROM HRIS_FLAT_VALUE_DETAIL F,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID={$id['MONTH_ID']}
                  ) Y
                WHERE F. EMPLOYEE_ID = {$id['EMPLOYEE_ID']}
                AND F.FISCAL_YEAR_ID = F.FISCAL_YEAR_ID
                AND F.FLAT_ID        = {$id['FLAT_ID']}";

        $statement = $this->adapter->query($sql);
        $rawResult = $statement->execute();
        return $rawResult->current();
    }

    public function getFlatValuesDetailById($flatValueId, $fiscalYearId, $employeeFilter, $monthId = null) {
        $employeeIn = EntityHelper::employeesIn($employeeFilter['companyId'], $employeeFilter['branchId'], $employeeFilter['departmentId'], $employeeFilter['positionId'], $employeeFilter['designationId'], $employeeFilter['serviceTypeId'], $employeeFilter['serviceEventTypeId'], $employeeFilter['employeeTypeId'], $employeeFilter['employeeId']);
        $sql = "SELECT * FROM HRIS_FLAT_VALUE_DETAIL WHERE FLAT_ID = {$flatValueId} AND FISCAL_YEAR_ID = {$fiscalYearId} AND EMPLOYEE_ID IN ( {$employeeIn} )";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function postFlatValuesDetail($data) {
        $sql = "
                DECLARE
                  V_FLAT_ID HRIS_FLAT_VALUE_DETAIL.FLAT_ID%TYPE := {$data['flatId']};
                  V_EMPLOYEE_ID HRIS_FLAT_VALUE_DETAIL.EMPLOYEE_ID%TYPE := {$data['employeeId']};
                  V_FLAT_VALUE HRIS_FLAT_VALUE_DETAIL.FLAT_VALUE%TYPE := {$data['flatValue']};
                  V_FISCAL_YEAR_ID HRIS_FLAT_VALUE_DETAIL.FISCAL_YEAR_ID%TYPE := {$data['fiscalYearId']};
                  V_OLD_FLAT_VALUE HRIS_FLAT_VALUE_DETAIL.FLAT_VALUE%TYPE;
                BEGIN
                  SELECT FLAT_VALUE
                  INTO V_OLD_FLAT_VALUE
                  FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                  UPDATE HRIS_FLAT_VALUE_DETAIL
                  SET FLAT_VALUE      = V_FLAT_VALUE
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT
                  INTO HRIS_FLAT_VALUE_DETAIL
                    (
                      FLAT_ID,
                      EMPLOYEE_ID,
                      FISCAL_YEAR_ID,
                      FLAT_VALUE,
                      CREATED_DT
                    )
                    VALUES
                    (
                      V_FLAT_ID,
                      V_EMPLOYEE_ID,
                      V_FISCAL_YEAR_ID,
                      V_FLAT_VALUE,
                      TRUNC(SYSDATE)
                    );
                END;
";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

}
