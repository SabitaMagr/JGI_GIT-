<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\SalarySheetDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class SalarySheetDetailRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(SalarySheetDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        return $this->gateway->delete([SalarySheetDetail::MONTH_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        return $this->gateway->select($id);
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

}
