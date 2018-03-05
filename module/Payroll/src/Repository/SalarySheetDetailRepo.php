<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Payroll\Model\SalarySheetDetail;
use Zend\Db\Adapter\AdapterInterface;

class SalarySheetDetailRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = SalarySheetDetail::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        return $this->tableGateway->delete([SalarySheetDetail::MONTH_ID => $id]);
    }

    public function fetchById($id) {
        return $this->tableGateway->select($id);
    }

    public function fetchSalarySheetDetail($sheetId) {
        $in = $this->fetchPayIdsAsArray();
        $sql = "SELECT P.*,E.FULL_NAME AS EMPLOYEE_NAME
                FROM
                  (SELECT *
                  FROM
                    (SELECT EMPLOYEE_ID,
                      PAY_ID,
                      VAL
                    FROM HRIS_SALARY_SHEET_DETAIL
                    WHERE SHEET_NO                ={$sheetId}
                    ) PIVOT (MAX(VAL) FOR PAY_ID IN ({$in}))
                  ) P
                JOIN HRIS_EMPLOYEES E
                ON (P.EMPLOYEE_ID=E.EMPLOYEE_ID)";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    public function fetchSalarySheetEmp($monthId, $employeeId) {
        $in = $this->fetchPayIdsAsArray();
        $sql = "SELECT P.*,E.FULL_NAME AS EMPLOYEE_NAME
                FROM
                  (SELECT *
                  FROM
                    (SELECT EMPLOYEE_ID,
                      PAY_ID,
                      VAL
                    FROM HRIS_SALARY_SHEET_DETAIL
                    WHERE SHEET_NO                =(SELECT SHEET_NO FROM HRIS_SALARY_SHEET WHERE MONTH_ID ={$monthId})
                    AND EMPLOYEE_ID               ={$employeeId}
                    ) PIVOT (MAX(VAL) FOR PAY_ID IN ({$in}))
                  ) P
                JOIN HRIS_EMPLOYEES E
                ON (P.EMPLOYEE_ID=E.EMPLOYEE_ID)";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    private function fetchPayIdsAsArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT PAY_ID FROM HRIS_PAY_SETUP WHERE STATUS ='E'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['PAY_ID']} AS P_{$row['PAY_ID']}";
            } else {
                $dbArray .= "{$row['PAY_ID']} AS P_{$row['PAY_ID']},";
            }
        }
        return $dbArray;
    }

    public function fetchPrevSumPayValue($employeeId, $fiscalYearId, $fiscalYearMonthNo) {
        $sql = "SELECT SSD.PAY_ID,
                  SUM(SSD.VAL) AS PREV_SUM_VAL
                FROM HRIS_SALARY_SHEET_DETAIL SSD
                JOIN HRIS_SALARY_SHEET SS
                ON (SSD.SHEET_NO =SS.SHEET_NO)
                JOIN HRIS_MONTH_CODE MC
                ON (SS.MONTH_ID             =MC.MONTH_ID)
                WHERE MC.FISCAL_YEAR_ID     ={$fiscalYearId}
                AND MC.FISCAL_YEAR_MONTH_NO <{$fiscalYearMonthNo}
                AND SSD.EMPLOYEE_ID         ={$employeeId}
                GROUP BY SSD.PAY_ID";
        return $this->rawQuery($sql);
    }

}
