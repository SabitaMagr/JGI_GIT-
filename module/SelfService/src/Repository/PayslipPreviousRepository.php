<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;

class PayslipPreviousRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function getPeriodList($companyCode) {
        $sql = "
                SELECT PERIOD_DT_CODE AS mcode,
                  DT_EDESC            AS mname
                FROM HR_PERIOD_DETAIL
                WHERE COMPANY_CODE='{$companyCode}'
                ORDER BY to_number(PERIOD_DT_CODE) ;";
        return $this->rawQuery($sql);
    }

    public function getPayslipDetail($companyCode, $employeeCode) {
        $sql = "SELECT SS.SHEET_NO,
                  SS.SAL_SHEET_CODE,
                  SSD.EMPLOYEE_CODE ,
                  E.EMPLOYEE_EDESC,
                  SSD.BASIC_SALARY,
                  SSD.BASIC_PERIOD,
                  E.EMPLOYEE_CODE,
                  SSD.CALC_BASIC,
                  SSD.GRADE_CODE,
                  SSD.MARITAL_STATUS,
                  SSD.PRESENT_DAYS,
                  SSD.WORK_DAYS ,
                  SS.PERIOD_DT_CODE,
                  SSD.OVERTIME_HOURS,
                  SSD.LEAVE_WITH_PAY_DAYS,
                  SSD.LEAVE_WITHOUT_PAY_DAYS,
                  SSD.GROSS_AMOUNT,
                  SSD.NET_AMOUNT,
                  SSD.EREMARKS,
                  SSD.NREMARKS,
                  SSD.HOLIDAYS,
                  SSD.ABSENT_DAYS,
                  SSD.HOLIDAYS,
                  SSD.DEPARTMENT_CODE,
                  (SELECT COMPANY_SETUP.COMPANY_EDESC
                  FROM COMPANY_SETUP
                  WHERE COMPANY_SETUP.COMPANY_CODE=E.COMPANY_CODE
                  ) AS COMPANY_NAME,
                  (SELECT HR_BRANCH_CODE.BRANCH_EDESC
                  FROM HR_BRANCH_CODE
                  WHERE HR_BRANCH_CODE.BRANCH_CODE=E.BRANCH_CODE
                  ) AS BRANCH_NAME,
                  (SELECT HR_DEPARTMENT_CODE.DEPARTMENT_EDESC
                  FROM HR_DEPARTMENT_CODE
                  WHERE HR_DEPARTMENT_CODE.DEPARTMENT_CODE=SSD.DEPARTMENT_CODE
                  ) AS DEPARTMENT_NAME,
                  (SELECT HR_GRADE_CODE.GRADE_EDESC
                  FROM HR_GRADE_CODE
                  WHERE HR_GRADE_CODE.GRADE_CODE=SSD.GRADE_CODE
                  ) AS GRADE_NAME,
                  (SELECT DT_EDESC
                  FROM HR_PERIOD_DETAIL
                  WHERE HR_PERIOD_DETAIL.PERIOD_DT_CODE=SS.PERIOD_DT_CODE
                  AND HR_PERIOD_DETAIL.COMPANY_CODE    =SS.COMPANY_CODE
                  ) AS MONTH
                FROM HR_SALARY_SHEET SS
                JOIN HR_SALARY_SHEET_DETAIL SSD
                ON (SS.SHEET_NO =SSD.SHEET_NO)
                JOIN HR_EMPLOYEE_SETUP E
                ON (SSD.EMPLOYEE_CODE =E.EMPLOYEE_CODE)
                WHERE SS.COMPANY_CODE ='{$companyCode}'
                AND SS.PERIOD_DT_CODE = 1
                AND SS.SALARY_TYPE    =0
                AND SSD.EMPLOYEE_CODE ='{$employeeCode}'";
        return $this->rawQuery($sql);
    }

}
