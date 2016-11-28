<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/4/16
 * Time: 12:06 PM
 */

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
use Zend\Db\TableGateway\TableGateway;

class MonthlyValueDetailRepo implements RepositoryInterface
{
    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(MonthlyValueDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->gateway->update($model->getArrayCopyForDB(), [MonthlyValueDetail::EMPLOYEE_ID=>$id[0],MonthlyValueDetail::MTH_ID=>$id[1]]);
    }

    public function fetchAll()
    {

    }

    public function filter($branchId, $departmentId, $designationId,$id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(["EMPLOYEE_ID", "FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], true);
        $select->from(['E' => "HR_EMPLOYEES"])
        ->join(['M' => MonthlyValueDetail::TABLE_NAME], 'M.'.MonthlyValueDetail::EMPLOYEE_ID.'=E.EMPLOYEE_ID', [MonthlyValueDetail::MTH_ID, MonthlyValueDetail::MTH_VALUE],Select::JOIN_LEFT);
        if ($branchId != -1) {
            $select->where(["E." . Branch::BRANCH_ID . "=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E." . Department::DEPARTMENT_ID . "=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E." . Designation::DESIGNATION_ID . "=$designationId"]);
        }
//        $select->where("M.".MonthlyValueDetail::MTH_ID."=".$id." OR M.".MonthlyValueDetail::MTH_ID." IS NULL");
        $select->where("M.".MonthlyValueDetail::MTH_ID."=".$id);
        $select->order("E.EMPLOYEE_ID ASC");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function fetchEmployees($branchId, $departmentId, $designationId)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(["EMPLOYEE_ID", "FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], true);
        $select->from(['E' => "HR_EMPLOYEES"]);
        if ($branchId != -1) {
            $select->where(["E." . Branch::BRANCH_ID . "=$branchId"]);
        }
        if ($departmentId != -1) {
            $select->where(["E." . Department::DEPARTMENT_ID . "=$departmentId"]);
        }
        if ($designationId != -1) {
            $select->where(["E." . Designation::DESIGNATION_ID . "=$designationId"]);
        }
        $select->order("E.EMPLOYEE_ID ASC");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
      return  $this->gateway->select([MonthlyValueDetail::MTH_ID=>$id[1],MonthlyValueDetail::EMPLOYEE_ID=>$id[0]])->current();
    }
}
