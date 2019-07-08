<?php

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Model\Months;
use Application\Repository\HrisRepository;
use Payroll\Model\SalarySheet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class SalarySheetRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, SalarySheet::TABLE_NAME);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        return $this->tableGateway->delete([SalarySheet::MONTH_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function (Select $select) {
                    $select->columns(Helper::convertColumnDateFormat($this->adapter, new SalarySheet(), [
                                'startDate',
                                'endDate',
                            ]), false);
                });
    }

    public function fetchById($id) {
        return $this->tableGateway->select([SalarySheet::SHEET_NO => $id]);
    }

    public function fetchByIds(array $ids) {
        return $this->tableGateway->select($ids);
    }

    public function fetchOneBy(array $ids) {
        return $this->tableGateway->select($ids)->current();
    }

    public function joinWithMonth($monthId = null, $employeeJoinDate = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([]);
        $select->from(['S' => SalarySheet::TABLE_NAME])
                ->join(['M' => Months::TABLE_NAME], 'S.' . SalarySheet::MONTH_ID . '=M.' . Months::MONTH_ID);
        if ($monthId != null) {
            $select->where([Months::MONTH_ID => $monthId]);
        }
        $select->where(["M." . Months::STATUS . " = " . "'E'"]);
        $select->where(["S." . SalarySheet::STATUS . " = " . "'E'"]);

        if ($employeeJoinDate != null) {
            $select->where(["'" . $employeeJoinDate . "'" . " <= " . "M." . Months::TO_DATE]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function generateSalShReport($sheetNo) {
        $this->executeStatement("BEGIN
                            HRIS_GEN_SAL_SH_REPORT({$sheetNo});
                        END;");
    }

    public function updateLoanPaymentFlag($employeeId, $sheetNo) {
        $this->executeStatement("BEGIN
                            hris_loan_payment_flag_change({$employeeId},{$sheetNo});
                        END;");
    }

    public function fetchAllSalaryType() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['*']);
        $select->from('HRIS_SALARY_TYPE');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function fetchEmployeeByGroup($monthId,$group) {
        $sql = "SELECT 
            employee_id,employee_code,full_name,'Y' AS CHECKED_FLAG
            FROM HRIS_EMPLOYEES 
            WHERE 
employee_id not in (select employee_id from HRIS_SALARY_SHEET_EMP_DETAIL where month_id={$monthId})            
AND GROUP_ID IN ({$group})";
        $data = $this->rawQuery($sql);
        return $data;
    }
    
    public function fetchGeneratedSheetByGroup($monthId,$group){
        $sql="select sheet_no from HRIS_SALARY_SHEET 
                where Month_Id={$monthId} and Group_Id in ($group)";
        $data = $this->rawQuery($sql);
        return $data;
    }
    
    public function insertPayrollEmp($empList) {
        $deleteSql = "delete from HRIS_PAYROLL_EMP_LIST";
        $this->executeStatement($deleteSql);
        foreach ($empList as $employeeId) {
            $tempSql = "INSERT INTO HRIS_PAYROLL_EMP_LIST VALUES ({$employeeId})";
            $this->executeStatement($tempSql);
        }
    }

}
