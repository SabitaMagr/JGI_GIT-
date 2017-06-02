<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class PayrollRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchEmployeeList() {
        $sql = "
                SELECT E.EMPLOYEE_ID,
                  CONCAT(CONCAT(CONCAT(INITCAP(TRIM(E.FIRST_NAME)),' '),
                  CASE
                    WHEN E.MIDDLE_NAME IS NOT NULL
                    THEN CONCAT(INITCAP(TRIM(E.MIDDLE_NAME)), ' ')
                    ELSE ''
                  END ),INITCAP(TRIM(E.LAST_NAME))) AS FULL_NAME
                FROM HRIS_EMPLOYEES E
                WHERE E.JOIN_DATE <= TRUNC(SYSDATE)
                AND E.RETIRED_FLAG ='N'
                AND IS_ADMIN       ='N'
                AND STATUS         ='E'
                ";
        $employeeListRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($employeeListRaw);
    }

    public function fetchFiscalYears() {
        $sql = "
                SELECT FISCAL_YEAR_ID                                                     AS FISCAL_YEAR_ID,
                  CONCAT(CONCAT(TO_CHAR(START_DATE,'YYYY'),'-'),TO_CHAR(END_DATE,'YYYY')) AS NAME,
                  START_DATE                                                              AS START_DATE,
                  END_DATE                                                                AS END_DATE
                FROM HRIS_FISCAL_YEARS ORDER BY START_DATE DESC
                ";
        $fiscalYearsRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($fiscalYearsRaw);
    }

}
