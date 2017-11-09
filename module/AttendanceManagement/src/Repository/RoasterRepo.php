<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\RoasterModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class RoasterRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(RoasterModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        $raw = EntityHelper::rawQueryResult($this->adapter, "
                SELECT EMPLOYEE_ID,
                  SHIFT_ID,
                  TO_CHAR(FOR_DATE,'DD-MON-YYYY') AS FOR_DATE,
                  TO_CHAR(FOR_DATE,'YYYY-MM-DD')  AS FOR_DATE_FORMATTED
                FROM HRIS_EMPLOYEE_SHIFT_ROASTER");
        return Helper::extractDbData($raw);
    }

    public function fetchById($id) {
        
    }

    public function merge($employeeId, $forDate, $shiftId) {
        EntityHelper::rawQueryResult($this->adapter, "
                DECLARE
                  V_EMPLOYEE_ID  NUMBER :={$employeeId};
                  V_FOR_DATE     DATE   :=TO_DATE('{$forDate}','DD-MON-YYYY');
                  V_SHIFT_ID_NEW NUMBER :={$shiftId};
                  V_SHIFT_ID_OLD NUMBER;
                BEGIN
                  SELECT SHIFT_ID
                  INTO V_SHIFT_ID_OLD
                  FROM HRIS_EMPLOYEE_SHIFT_ROASTER
                  WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
                  AND FOR_DATE      =V_FOR_DATE;
                  UPDATE HRIS_EMPLOYEE_SHIFT_ROASTER
                  SET SHIFT_ID     = V_SHIFT_ID_NEW
                  WHERE EMPLOYEE_ID=V_EMPLOYEE_ID
                  AND FOR_DATE     =V_FOR_DATE;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT
                  INTO HRIS_EMPLOYEE_SHIFT_ROASTER
                    (
                      EMPLOYEE_ID,
                      FOR_DATE,
                      SHIFT_ID
                    )
                    VALUES
                    (
                      V_EMPLOYEE_ID,
                      V_FOR_DATE,
                      V_SHIFT_ID_NEW
                    );
                END;");
    }

}
