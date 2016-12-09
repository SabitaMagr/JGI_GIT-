<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 4:02 PM
 */
namespace LeaveManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Model\LeaveMaster;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class LeaveBalanceRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    private $leaveTableGateway;
    private $employeeTableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LeaveAssign::TABLE_NAME,$adapter);
        $this->leaveTableGateway = new TableGateway(LeaveMaster::TABLE_NAME,$adapter);
        $this->employeeTableGateway = new TableGateway("HR_EMPLOYEES",$adapter);
    }

    public function add(Model $model)
    {
        // TODO: Implement add() method.
    }
    public function getAllLeave(){
        $sql = "SELECT * FROM HR_LEAVE_MASTER_SETUP WHERE STATUS='E' ORDER BY LEAVE_ID";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    public function getAllEmployee($emplyoeeId,$branchId,$departmentId,$designationId,$positionId,$serviceTypeId,$serviceEventTypeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("E.FIRST_NAME AS FIRST_NAME"),
            new Expression("E.MIDDLE_NAME AS MIDDLE_NAME"),
            new Expression("E.LAST_NAME AS LAST_NAME"),
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID")
        ], true);

        $select->from(['E' => "HR_EMPLOYEES"]);

        $select->where([
            "E.STATUS='E'"
        ]);

        if($emplyoeeId!=-1){
            $select->where([
                "E.EMPLOYEE_ID=".$emplyoeeId
            ]);
        }
        if($branchId!=-1){
            $select->where([
                "E.BRANCH_ID=".$branchId
            ]);
        }
        if($departmentId!=-1){
            $select->where([
                "E.DEPARTMENT_ID=".$departmentId
            ]);
        }
        if($designationId!=-1){
            $select->where([
                "E.DESIGNATION_ID=".$designationId
            ]);
        }
        if($positionId!=-1){
            $select->where([
                "E.POSITION_ID=".$positionId
            ]);
        }
        if($serviceTypeId!=-1){
            $select->where([
                "E.SERVICE_TYPE_ID=".$serviceTypeId
            ]);
        }
        if($serviceEventTypeId!=-1){
            $select->where([
                "E.SERVICE_EVENT_TYPE_ID=".$serviceEventTypeId
            ]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function getByEmpIdLeaveId($employeeId,$leaveId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.BALANCE AS BALANCE"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
        ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['L'=>'HR_LEAVE_MASTER_SETUP'],"L.LEAVE_ID=LA.LEAVE_ID",['LEAVE_CODE','LEAVE_ENAME']);

        $select->where([
            "L.STATUS='E'",
            "E.STATUS='E'",
            "LA.EMPLOYEE_ID=".$employeeId,
            "LA.LEAVE_ID=".$leaveId
        ]);
        $select->order(['L.LEAVE_ID']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id)
    {
        // TODO: Implement fetchById() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
    
    public function getOnlyCarryForwardedRecord(){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.BALANCE AS BALANCE"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
        ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['L'=>'HR_LEAVE_MASTER_SETUP'],"L.LEAVE_ID=LA.LEAVE_ID",['LEAVE_CODE','LEAVE_ENAME']);

        $select->where([
            "L.STATUS='E'",
            "E.STATUS='E'",
            "L.CASHABLE='N'",
            "L.CARRY_FORWARD='Y'"
        ]);
        $select->order(['LA.EMPLOYEE_ID']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
     
       // $result =  $this->tableGateway->select();
        
        $record = [];
        foreach($result as $row){
            array_push($record,[
                'EMPLOYEE_ID'=>$row['EMPLOYEE_ID'],
                'LEAVE_ID'=>$row['LEAVE_ID'],
                'BALANCE'=>$row['BALANCE'],
                'TOTAL_DAYS'=>$row['TOTAL_DAYS']
            ]);
        }
        return $record;
    }
}