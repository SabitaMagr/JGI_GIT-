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
            $employeeCondition = " AND COMPANY_ID = {$companyId}";
        }

        if ($groupId != null) {
            $employeeCondition = " AND GROUP_ID = {$groupId}";
        }
        $sql = "SELECT * FROM (SELECT *
                FROM HRIS_SS_PAY_VALUE_MODIFIED
                WHERE EMPLOYEE_ID IN
                  (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE STATUS ='E' {$employeeCondition}
                  ) AND MONTH_ID ={$monthId}) PIVOT (MAX(VAL) FOR PAY_ID IN ({$csv}))";
        print $sql;
        exit;
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

}
