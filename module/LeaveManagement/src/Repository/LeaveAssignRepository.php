<?php

namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveAssign;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveAssignRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [LeaveAssign::LEAVE_ID => $id[0], LeaveAssign::EMPLOYEE_ID => $id[1]]);
        EntityHelper::rawQueryResult($this->adapter, "
            BEGIN
              HRIS_RECALCULATE_LEAVE({$id[1]},{$id[0]});
            END;");
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchByEmployeeId($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => LeaveAssign::TABLE_NAME])
                ->join(['S' => 'HRIS_LEAVE_MASTER_SETUP'], 'A.LEAVE_ID=S.LEAVE_ID');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filter($branchId, $departmentId, $genderId, $designationId, $serviceTypeId, $employeeId, $companyId, $positionId,$employeeTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], NULL, NULL, NULL, NULL, 'E'), true);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], null, null, null, [new Expression("E.EMPLOYEE_ID")], "E"), true);
        $select->from(['E' => "HRIS_EMPLOYEES"])
                ->join(['DE' => 'HRIS_DESIGNATIONS'], 'DE.DESIGNATION_ID=E.DESIGNATION_ID', ["DESIGNATION_ID", "DESIGNATION_TITLE" => new Expression("INITCAP(DE.DESIGNATION_TITLE)")], "left")
                ->join(['B' => 'HRIS_BRANCHES'], 'B.BRANCH_ID=E.BRANCH_ID', ["BRANCH_ID", "BRANCH_NAME" => new Expression("INITCAP(B.BRANCH_NAME)")], "left");
        $select->where(["E.STATUS='E'"]);
        $select->where(["E.RETIRED_FLAG='N'"]);
        
        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $select->where([
                "E.EMPLOYEE_TYPE= '{$employeeTypeId}'"
            ]);
        }
        
        if ($employeeId != -1) {
            $select->where(["E.EMPLOYEE_ID=$employeeId"]);
        }
        if ($companyId != -1) {
            $select->where(["E.COMPANY_ID=$companyId"]);
        }
        if ($branchId != -1) {
            $select->where(["E.BRANCH_ID=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E.DEPARTMENT_ID=$departmentId"]);
        }
        if ($genderId != -1) {
            $select->where(["E.GENDER_ID= $genderId"]);
        }
        if ($designationId != -1) {
            $select->where(["E.DESIGNATION_ID=$designationId"]);
        }
        if ($positionId != -1) {
            $select->where(["E.POSITION_ID=$positionId"]);
        }
        if ($serviceTypeId != -1) {
            $select->where(["E.SERVICE_TYPE_ID=$serviceTypeId"]);
        }
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filterByLeaveEmployeeId($leaveId, $employeeId) {
        $result = $this->tableGateway->select([LeaveAssign::LEAVE_ID => $leaveId, LeaveAssign::EMPLOYEE_ID => $employeeId]);
        return $result->current();
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([LeaveAssign::EMPLOYEE_LEAVE_ASSIGN_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->delete([LeaveAssign::EMPLOYEE_LEAVE_ASSIGN_ID => $id]);
    }

    public function updatePreYrBalance($employeeId, $leaveId, $preYrBalance, $totalDays, $balance) {
        $this->tableGateway->update([LeaveAssign::PREVIOUS_YEAR_BAL => $preYrBalance, LeaveAssign::TOTAL_DAYS => $totalDays, LeaveAssign::BALANCE => $balance], [LeaveAssign::EMPLOYEE_ID => $employeeId, LeaveAssign::LEAVE_ID => $leaveId]);
    }

}
