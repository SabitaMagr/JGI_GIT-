<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:31 PM
 */

namespace AttendanceManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Model\ShiftSetup;
use Setup\Model\Department;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Zend\Db\Sql\Expression;

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

        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class,
                [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],null,null,null,[new Expression("E.EMPLOYEE_ID")],"E"), true);
        $select->from(['E' => "HRIS_EMPLOYEES"])
            ->join(['B' => 'HRIS_BRANCHES'], 'B.BRANCH_ID=E.BRANCH_ID', ["BRANCH_ID", "BRANCH_NAME"=>new Expression("INITCAP(B.BRANCH_NAME)")],"left")
            ->join(['DEP' => Department::TABLE_NAME], 'DEP.' . Department::DEPARTMENT_ID . '=E.' . Department::DEPARTMENT_ID . '', [Department::DEPARTMENT_ID, "DEPARTMENT_NAME"=>new Expression("INITCAP(DEP.DEPARTMENT_NAME)")],"left")
            ->join(['DE' => 'HRIS_DESIGNATIONS'], 'DE.DESIGNATION_ID=E.DESIGNATION_ID', ["DESIGNATION_ID", "DESIGNATION_TITLE"=>new Expression("INITCAP(DE.DESIGNATION_TITLE)")],"left")
            ->join(['P' => Position::TABLE_NAME], 'P.' . Position::POSITION_ID . '=E.' . Position::POSITION_ID . '', [Position::POSITION_ID, "POSITION_NAME"=>new Expression("INITCAP(P.POSITION_NAME)")],"left")
            ->join(['ST' => ServiceType::TABLE_NAME], 'ST.' . ServiceType::SERVICE_TYPE_ID . '=E.' . ServiceType::SERVICE_TYPE_ID . '', [ServiceType::SERVICE_TYPE_ID, "SERVICE_TYPE_NAME"=>new Expression("INITCAP(ST.SERVICE_TYPE_NAME)")],"left");
       $select->where(["E.STATUS='E'"]);
       $select->where(["E.RETIRED_FLAG='N'"]);
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
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filterByEmployeeId($employeeId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(['SA' => ShiftAssign::TABLE_NAME])
            ->join(['SH' => ShiftSetup::TABLE_NAME], 'SA.' . ShiftSetup::SHIFT_ID . '=SH.' . ShiftSetup::SHIFT_ID . '', ["SHIFT_ENAME"=>new Expression("INITCAP(SH.SHIFT_ENAME)")],"left");
        $select->where(["SA.".ShiftAssign::STATUS." ='E'","SA.".ShiftAssign::EMPLOYEE_ID." =$employeeId"]);
        $select->order('SA.'.ShiftAssign::CREATED_DT.' DESC');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        $result = $this->tableGateway->select([ShiftAssign::EMPLOYEE_ID."=".$employeeId, ShiftAssign::STATUS=>'E']);
        return $result->current();
    }
}