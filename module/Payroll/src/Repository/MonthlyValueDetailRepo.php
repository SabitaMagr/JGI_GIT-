<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\MonthlyValueDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class MonthlyValueDetailRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(MonthlyValueDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [MonthlyValueDetail::EMPLOYEE_ID => $id[0], MonthlyValueDetail::MTH_ID => $id[1]]);
    }

    public function fetchAll() {
        
    }

    public function delete($id) {
        
    }

    public function fetchById($id) {
        $sql = "
                SELECT MTH_VALUE
                FROM HRIS_MONTHLY_VALUE_DETAIL
                WHERE EMPLOYEE_ID = {$id['employeeId']}
                AND MONTH_ID      = {$id['monthId']}
                AND MTH_ID        = {$id['mthId']}";

        $statement = $this->adapter->query($sql);
        $rawResult = $statement->execute();
        $result = $rawResult->current();
        return $result != null ? $result['MTH_VALUE'] : 0;
    }

    public function getMonthlyValuesDetailById($monthlyValueId, $fiscalYearId, $employeeFilter, $monthId = null) {
        $employeeIn = EntityHelper::employeesIn($employeeFilter['companyId'], $employeeFilter['branchId'], $employeeFilter['departmentId'], $employeeFilter['positionId'], $employeeFilter['designationId'], $employeeFilter['serviceTypeId'], $employeeFilter['serviceEventTypeId'], $employeeFilter['employeeTypeId'], $employeeFilter['employeeId']);
        $sql = "SELECT * FROM HRIS_MONTHLY_VALUE_DETAIL WHERE MTH_ID = {$monthlyValueId} AND FISCAL_YEAR_ID = {$fiscalYearId} AND EMPLOYEE_ID IN ( {$employeeIn} )";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function postMonthlyValuesDetail($data) {
        $sql = "
                DECLARE
                  V_MTH_ID HRIS_MONTHLY_VALUE_DETAIL.MTH_ID%TYPE := {$data['mthId']};
                  V_EMPLOYEE_ID HRIS_MONTHLY_VALUE_DETAIL.EMPLOYEE_ID%TYPE := {$data['employeeId']};
                  V_MTH_VALUE HRIS_MONTHLY_VALUE_DETAIL.MTH_VALUE%TYPE := {$data['mthValue']};
                  V_FISCAL_YEAR_ID HRIS_MONTHLY_VALUE_DETAIL.FISCAL_YEAR_ID%TYPE := {$data['fiscalYearId']};
                  V_MONTH_ID HRIS_MONTHLY_VALUE_DETAIL.MONTH_ID%TYPE := {$data['monthId']};
                  V_OLD_MTH_VALUE HRIS_MONTHLY_VALUE_DETAIL.MTH_VALUE%TYPE;
                BEGIN
                  SELECT MTH_VALUE
                  INTO V_OLD_MTH_VALUE
                  FROM HRIS_MONTHLY_VALUE_DETAIL
                  WHERE MTH_ID       = V_MTH_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID
                  AND MONTH_ID       = V_MONTH_ID;
                  UPDATE HRIS_MONTHLY_VALUE_DETAIL
                  SET MTH_VALUE      = V_MTH_VALUE
                  WHERE MTH_ID       = V_MTH_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID
                  AND MONTH_ID       = V_MONTH_ID;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT
                  INTO HRIS_MONTHLY_VALUE_DETAIL
                    (
                      MTH_ID,
                      EMPLOYEE_ID,
                      FISCAL_YEAR_ID,
                      MONTH_ID,
                      MTH_VALUE,
                      CREATED_DT
                    )
                    VALUES
                    (
                      V_MTH_ID,
                      V_EMPLOYEE_ID,
                      V_FISCAL_YEAR_ID,
                      V_MONTH_ID,
                      V_MTH_VALUE,
                      TRUNC(SYSDATE)
                    );
                END;
";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getPositionMonthlyValue($monthId) {
        $sql = "
            SELECT *
            FROM
              ( SELECT MTH_ID,POSITION_ID,ASSIGNED_VALUE FROM HRIS_POSITION_MONTHLY_VALUE WHERE MONTH_ID ={$monthId}
              ) PIVOT ( MAX(ASSIGNED_VALUE) FOR MTH_ID IN ({$this->fetchMonthlyValueAsDbArray()}) )";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    private function fetchMonthlyValueAsDbArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT MTH_ID FROM HRIS_MONTHLY_VALUE_SETUP WHERE STATUS ='E'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['MTH_ID']}";
            } else {
                $dbArray .= "{$row['MTH_ID']},";
            }
        }
        return $dbArray;
    }

    public function setPositionMonthlyValue($monthId, $positionId, $mthId, $assignedValue) {
        $sql = "
                DECLARE
                  V_MONTH_ID HRIS_POSITION_MONTHLY_VALUE.MONTH_ID%TYPE             := {$monthId};
                  V_MTH_ID HRIS_POSITION_MONTHLY_VALUE.MTH_ID%TYPE                 := {$mthId};
                  V_POSITION_ID HRIS_POSITION_MONTHLY_VALUE.POSITION_ID%TYPE       := {$positionId};
                  V_ASSIGNED_VALUE HRIS_POSITION_MONTHLY_VALUE.ASSIGNED_VALUE%TYPE := {$assignedValue};
                  V_OLD_ASSIGNED_VALUE HRIS_POSITION_MONTHLY_VALUE.ASSIGNED_VALUE%TYPE;
                BEGIN
                  SELECT ASSIGNED_VALUE
                  INTO V_OLD_ASSIGNED_VALUE
                  FROM HRIS_POSITION_MONTHLY_VALUE
                  WHERE MTH_ID    = V_MTH_ID
                  AND POSITION_ID = V_POSITION_ID
                  AND MONTH_ID    = V_MONTH_ID;
                  UPDATE HRIS_POSITION_MONTHLY_VALUE
                  SET ASSIGNED_VALUE = V_ASSIGNED_VALUE
                  WHERE MTH_ID       = V_MTH_ID
                  AND POSITION_ID    = V_POSITION_ID
                  AND MONTH_ID       = V_MONTH_ID;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT
                  INTO HRIS_POSITION_MONTHLY_VALUE
                    (
                      MTH_ID,
                      POSITION_ID,
                      MONTH_ID,
                      ASSIGNED_VALUE
                    )
                    VALUES
                    (
                      V_MTH_ID,
                      V_POSITION_ID,
                      V_MONTH_ID,
                      V_ASSIGNED_VALUE
                    );
                END;";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

}
