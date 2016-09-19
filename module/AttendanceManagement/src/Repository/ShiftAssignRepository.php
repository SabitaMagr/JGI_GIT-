<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:31 PM
 */

namespace AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
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

    public function filter($branchId, $departmentId,$designationId,$positionId,$serviceTypeId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(["EMPLOYEE_ID", "FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], true);
        $select->from(['E' => "HR_EMPLOYEES"])
            ->join(['B' => 'HR_BRANCHES'], 'B.BRANCH_ID=E.BRANCH_ID', ["BRANCH_ID", "BRANCH_NAME"])
            ->join(['DEP' => Department::TABLE_NAME], 'DEP.'.Department::DEPARTMENT_ID.'=E.'.Department::DEPARTMENT_ID.'', [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME])
            ->join(['DE' => 'HR_DESIGNATIONS'], 'DE.DESIGNATION_ID=E.DESIGNATION_ID', ["DESIGNATION_ID", "DESIGNATION_TITLE"])
            ->join(['P' => Position::TABLE_NAME], 'P.'.Position::POSITION_ID.'=E.'.Position::POSITION_ID.'', [Position::POSITION_ID, Position::POSITION_NAME])
            ->join(['ST' => ServiceType::TABLE_NAME], 'ST.'.ServiceType::SERVICE_TYPE_ID.'=E.'.ServiceType::SERVICE_TYPE_ID.'', [ServiceType::SERVICE_TYPE_ID, ServiceType::SERVICE_TYPE_NAME])
        ;
        if ($branchId != -1) {
            $select->where(["E.BRANCH_ID=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["DEPARTMENT_ID=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E.DESIGNATION_ID=$designationId"]);
        }
        if ($positionId != -1) {
            $select->where(['E.'.Position::POSITION_ID."=$positionId"]);
        }
        if ($serviceTypeId != -1) {
            $select->where(['E.'.ServiceType::SERVICE_TYPE_ID."=$serviceTypeId"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filterByEmployeeId( $employeeId)
    {
        $result = $this->tableGateway->select([ShiftAssign::EMPLOYEE_ID => $employeeId]);
        return $result->current();

    }

}