<?php 

namespace Overtime\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Overtime\Model\CompulsoryOvertime;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class OvertimeAutomationRepository {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(CompulsoryOvertime::TABLE_NAME, $adapter);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CompulsoryOvertime::class, NULL, [
                                CompulsoryOvertime::START_DATE,
                                CompulsoryOvertime::END_DATE,
                                    ], NULL, NULL, NULL, NULL, FALSE, FALSE, [
                                CompulsoryOvertime::EARLY_OVERTIME_HR,
                                CompulsoryOvertime::LATE_OVERTIME_HR
                            ]), false);
                });
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(CompulsoryOvertime::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CompulsoryOvertime::class, NULL, [
                    CompulsoryOvertime::START_DATE,
                    CompulsoryOvertime::END_DATE,
                        ], NULL, NULL, NULL, NULL, FALSE, FALSE, [
                    CompulsoryOvertime::EARLY_OVERTIME_HR,
                    CompulsoryOvertime::LATE_OVERTIME_HR
                ]), false);
        $select->where([CompulsoryOvertime::COMPULSORY_OVERTIME_ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchAssignedEmployees($id) {
        $sql = "SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEE_COMPULSORY_OT WHERE COMPULSORY_OVERTIME_ID= {$id}";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($rawResult);
    }

    public function wizardProcedure($earlyOvertimeHr, $lateOvertimeHr, $startDate, $endDate, $employeeList, $employeeId = null, $id = null) {
        $employeeAssign = '';


        foreach ($employeeList as $employee) {
            $employeeAssign = $employeeAssign . "
                 INSERT
                      INTO HRIS_EMPLOYEE_COMPULSORY_OT
                        (
                          EMPLOYEE_ID,
                          COMPULSORY_OVERTIME_ID
                        )
                        VALUES
                        (
                          {$employee},V_COMPULSORY_OVERTIME_ID
                        );";
        }
        if ($id == "") {
            $sql = "
                    DECLARE
                      V_COMPULSORY_OVERTIME_ID NUMBER;
                      V_EARLY_OVERTIME_HR      NUMBER:={$earlyOvertimeHr};
                      V_LATE_OVERTIME_HR       NUMBER:={$lateOvertimeHr};
                      V_END_DATE               DATE :=TO_DATE('{$endDate}','DD-MON-YYYY');
                      V_EMPLOYEE_ID            NUMBER :={$employeeId};
                      V_STATUS                 CHAR(1 BYTE):='E';
                    BEGIN
                      SELECT NVL(MAX(COMPULSORY_OVERTIME_ID),0)+1
                      INTO V_COMPULSORY_OVERTIME_ID
                      FROM HRIS_COMPULSORY_OVERTIME;
                      INSERT
                      INTO HRIS_COMPULSORY_OVERTIME
                        (
                          COMPULSORY_OVERTIME_ID,
                          EARLY_OVERTIME_HR,
                          LATE_OVERTIME_HR,
                          START_DATE,
                          END_DATE,
                          CREATED_DT,
                          CREATED_BY,
                          STATUS
                        )
                        VALUES
                        (
                          V_COMPULSORY_OVERTIME_ID,
                          V_EARLY_OVERTIME_HR,
                          V_LATE_OVERTIME_HR,
                          V_START_DATE,
                          V_END_DATE,
                          TRUNC(SYSDATE),
                          V_EMPLOYEE_ID,
                          V_STATUS
                        );
                     {$employeeAssign}
                      COMMIT;
                    END;";
        } else {
            $sql = "
                DECLARE
                  V_COMPULSORY_OVERTIME_ID NUMBER:={$id};
                  V_EARLY_OVERTIME_HR      NUMBER      :={$earlyOvertimeHr};
                  V_LATE_OVERTIME_HR       NUMBER      :={$lateOvertimeHr};
                  V_START_DATE             DATE        :=TO_DATE('{$startDate}','DD-MON-YYYY');
                  V_END_DATE               DATE        :=TO_DATE('{$endDate}','DD-MON-YYYY');
                  V_EMPLOYEE_ID            NUMBER      :={$employeeId};
                  V_STATUS                 CHAR(1 BYTE):='E';
                BEGIN
                  UPDATE HRIS_COMPULSORY_OVERTIME
                  SET EARLY_OVERTIME_HR       =V_EARLY_OVERTIME_HR,
                    LATE_OVERTIME_HR          =V_LATE_OVERTIME_HR,
                    START_DATE                =V_START_DATE,
                    END_DATE                  =V_END_DATE,
                    MODIFIED_DT               =TRUNC(SYSDATE),
                    MODIFIED_BY               =V_EMPLOYEE_ID
                  WHERE COMPULSORY_OVERTIME_ID=V_COMPULSORY_OVERTIME_ID;
                  DELETE
                  FROM HRIS_EMPLOYEE_COMPULSORY_OT
                  WHERE COMPULSORY_OVERTIME_ID=V_COMPULSORY_OVERTIME_ID;
                  {$employeeAssign}
                  COMMIT;
                END;";
        }

        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

}
