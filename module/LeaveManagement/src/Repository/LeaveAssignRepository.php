<?php
namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;

class LeaveAssignRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = LeaveAssign::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
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

    public function filter($branchId, $departmentId, $genderId, $designationId, $serviceTypeId, $employeeId, $companyId, $positionId, $employeeTypeId, $leaveId): array {
        $searchCondition = EntityHelper::getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, null, $employeeTypeId, $employeeId, $genderId);
        $sql = "SELECT C.COMPANY_NAME,
                  B.BRANCH_NAME,
                  DEP.DEPARTMENT_NAME,
                  E.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE,
                  E.FULL_NAME,
                  ELA.LEAVE_ID,
                  ELA.PREVIOUS_YEAR_BAL,
                  ELA.TOTAL_DAYS,
                  ELA.BALANCE
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_EMPLOYEE_LEAVE_ASSIGN ELA
                ON (E.EMPLOYEE_ID = ELA.EMPLOYEE_ID)
                LEFT JOIN HRIS_COMPANY C
                ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_BRANCHES B
                ON (E.BRANCH_ID=B.BRANCH_ID)
                LEFT JOIN HRIS_DEPARTMENTS DEP
                ON (E.DEPARTMENT_ID=DEP.DEPARTMENT_ID)
                WHERE 1            =1 
                AND ELA.LEAVE_ID   ={$leaveId}
                {$searchCondition}
                ORDER BY C.COMPANY_NAME,B.BRANCH_NAME,DEP.DEPARTMENT_NAME,E.FULL_NAME";
        return $this->rawQuery($sql);
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
