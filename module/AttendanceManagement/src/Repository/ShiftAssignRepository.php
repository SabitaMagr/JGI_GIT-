<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:31 PM
 */

namespace AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftSetup;
use LeaveManagement\Model\LeaveAssign;
use Setup\Model\Department;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use AttendanceManagement\Model\ShiftAssign;

class ShiftAssignRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(ShiftAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[ShiftAssign::EMPLOYEE_ID."=$id[0]",ShiftAssign::SHIFT_ID." =$id[1]"]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
    }

    public function delete($id)
    {
    }

    public function filter($branchId, $departmentId, $designationId, $positionId, $serviceTypeId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(["EMPLOYEE_ID", "FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], true);
        $select->from(['E' => "HR_EMPLOYEES"])
            ->join(['B' => 'HR_BRANCHES'], 'B.BRANCH_ID=E.BRANCH_ID', ["BRANCH_ID", "BRANCH_NAME"],"left")
            ->join(['DEP' => Department::TABLE_NAME], 'DEP.' . Department::DEPARTMENT_ID . '=E.' . Department::DEPARTMENT_ID . '', [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME],"left")
            ->join(['DE' => 'HR_DESIGNATIONS'], 'DE.DESIGNATION_ID=E.DESIGNATION_ID', ["DESIGNATION_ID", "DESIGNATION_TITLE"],"left")
            ->join(['P' => Position::TABLE_NAME], 'P.' . Position::POSITION_ID . '=E.' . Position::POSITION_ID . '', [Position::POSITION_ID, Position::POSITION_NAME],"left")
            ->join(['ST' => ServiceType::TABLE_NAME], 'ST.' . ServiceType::SERVICE_TYPE_ID . '=E.' . ServiceType::SERVICE_TYPE_ID . '', [ServiceType::SERVICE_TYPE_ID, ServiceType::SERVICE_TYPE_NAME],"left");
        if ($branchId != -1) {
            $select->where(["E.BRANCH_ID=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E.".Department::DEPARTMENT_ID."=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E.DESIGNATION_ID=$designationId"]);
        }
        if ($positionId != -1) {
            $select->where(['E.' . Position::POSITION_ID . "=$positionId"]);
        }
        if ($serviceTypeId != -1) {
            $select->where(['E.' . ServiceType::SERVICE_TYPE_ID . "=$serviceTypeId"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filterByEmployeeId($employeeId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(['SA' => ShiftAssign::TABLE_NAME])
            ->join(['SH' => ShiftSetup::TABLE_NAME], 'SA.' . ShiftSetup::SHIFT_ID . '=SH.' . ShiftSetup::SHIFT_ID . '', [ShiftSetup::SHIFT_ENAME]);
        $select->where(["SA.".ShiftAssign::STATUS." ='E'","SA.".ShiftAssign::EMPLOYEE_ID." =$employeeId"]);
        $select->order('SA.'.ShiftAssign::CREATED_DT.' DESC');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return     $result->current();



    }

}