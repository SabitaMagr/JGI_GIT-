<?php

namespace Payroll\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\MonthlyValueDetail;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Helper\EntityHelper;
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

    public function filter($branchId, $departmentId, $designationId, $id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], null, null, null, [new Expression("E.EMPLOYEE_ID")], "E"), true);
        $select->from(['E' => "HRIS_EMPLOYEES"])
                ->join(['M' => MonthlyValueDetail::TABLE_NAME], 'M.' . MonthlyValueDetail::EMPLOYEE_ID . '=E.EMPLOYEE_ID', [MonthlyValueDetail::MTH_ID, MonthlyValueDetail::MTH_VALUE], Select::JOIN_LEFT);
        if ($branchId != -1) {
            $select->where(["E." . Branch::BRANCH_ID . "=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E." . Department::DEPARTMENT_ID . "=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E." . Designation::DESIGNATION_ID . "=$designationId"]);
        }
        $select->where("M." . MonthlyValueDetail::MTH_ID . "=" . $id);
        $select->order("E.EMPLOYEE_ID ASC");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

    public function fetchEmployees($branchId, $departmentId, $designationId, $companyId = null, $employeeId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], null, null, null, [new Expression("E.EMPLOYEE_ID")], "E"), true);
        $select->from(['E' => "HRIS_EMPLOYEES"]);
        if ($branchId != -1) {
            $select->where(["E." . Branch::BRANCH_ID . "=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E." . Department::DEPARTMENT_ID . "=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E." . Designation::DESIGNATION_ID . "=$designationId"]);
        }
        if ($companyId != null && $companyId != -1) {
            $select->where(["E." . HrEmployees::COMPANY_ID . "=$companyId"]);
        }
        if ($employeeId != null && $employeeId != -1) {
            $select->where(["E." . HrEmployees::EMPLOYEE_ID . "=$employeeId"]);
        }
        $select->order("E.EMPLOYEE_ID ASC");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        return $this->gateway->select([MonthlyValueDetail::MTH_ID => $id[1], MonthlyValueDetail::EMPLOYEE_ID => $id[0]])->current();
    }

}
