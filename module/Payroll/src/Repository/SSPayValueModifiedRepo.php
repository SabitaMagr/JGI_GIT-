<?php

namespace Payroll\Repository;

use Application\Repository\HrisRepository;
use Payroll\Model\SSPayValueModified;
use Zend\Db\Adapter\AdapterInterface;

class SSPayValueModifiedRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName === null) {
            $tableName = SSPayValueModified::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function filter($monthId, $companyId = null, $groupId = null) {
        $csv = $this->fetchCSVSSRules();
        $employeeCondition = "";
        if ($companyId != null) {
            $employeeCondition = " AND E.COMPANY_ID = {$companyId}";
        }

        if ($groupId != null) {
            $employeeCondition = " AND E.GROUP_ID = {$groupId}";
        }
        $sql = "SELECT E.EMPLOYEE_ID,
                  E.FULL_NAME,
                  C.COMPANY_ID,
                  C.COMPANY_NAME,
                  SSG.GROUP_ID,
                  SSG.GROUP_NAME,
                  PV.*
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_COMPANY C ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_SALARY_SHEET_GROUP SSG ON (E.GROUP_ID=SSG.GROUP_ID)
                LEFT JOIN
                  (SELECT *
                  FROM
                    (SELECT * FROM HRIS_SS_PAY_VALUE_MODIFIED WHERE MONTH_ID ={$monthId}
                    ) PIVOT (MAX(VAL) FOR PAY_ID IN ({$csv}))
                  ) PV
                ON (E.EMPLOYEE_ID=PV.EMPLOYEE_ID)
                WHERE E.STATUS   ='E' {$employeeCondition} ORDER BY C.COMPANY_NAME,SSG.GROUP_NAME,E.FULL_NAME";
        return $this->rawQuery($sql);
    }

    private function fetchCSVSSRules(): string {
        $sql = "SELECT PAY_ID
                FROM HRIS_PAY_SETUP
                WHERE INCLUDE_IN_SALARY='Y'
                AND PAY_TYPE_FLAG     IN ('A','D')
                AND STATUS ='E'
                ORDER BY PRIORITY_INDEX";
        $statement = $this->adapter->query($sql);
        $rawList = $statement->execute();

        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['PAY_ID']} AS H_{$row['PAY_ID']}";
            } else {
                $dbArray .= "{$row['PAY_ID']} AS H_{$row['PAY_ID']},";
            }
        }
        return $dbArray;
    }

    public function bulkEdit($data) {
        foreach ($data as $value) {
            $this->createOrUpdate($value['MONTH_ID'], $value['EMPLOYEE_ID'], $value['PAY_ID'], $value['VAL']);
        }
    }

    private function createOrUpdate($m, $e, $p, $v) {
        $sql = "DECLARE
                  V_MONTH_ID HRIS_SS_PAY_VALUE_MODIFIED.MONTH_ID%TYPE      :={$m};
                  V_EMPLOYEE_ID HRIS_SS_PAY_VALUE_MODIFIED.EMPLOYEE_ID%TYPE:={$e};
                  V_PAY_ID HRIS_SS_PAY_VALUE_MODIFIED.PAY_ID%TYPE          :={$p};
                  V_VAL HRIS_SS_PAY_VALUE_MODIFIED.VAL%TYPE                :={$v};
                  V_ROW_COUNT NUMBER;
                BEGIN
                  SELECT COUNT(*)
                  INTO V_ROW_COUNT
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE MONTH_ID  =V_MONTH_ID
                  AND EMPLOYEE_ID = V_EMPLOYEE_ID
                  AND PAY_ID      = V_PAY_ID;
                  IF (V_ROW_COUNT >0 ) THEN
                    UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                    SET VAL         =V_VAL
                    WHERE MONTH_ID  =V_MONTH_ID
                    AND EMPLOYEE_ID = V_EMPLOYEE_ID
                    AND PAY_ID      = V_PAY_ID;
                  ELSE
                    INSERT
                    INTO HRIS_SS_PAY_VALUE_MODIFIED
                      (
                        MONTH_ID,
                        EMPLOYEE_ID,
                        PAY_ID,
                        VAL
                      )
                      VALUES
                      (
                        V_MONTH_ID,
                        V_EMPLOYEE_ID,
                        V_PAY_ID,
                        V_VAL
                      );
                  END IF;
                END;";
        $this->executeStatement($sql);
    }

}
