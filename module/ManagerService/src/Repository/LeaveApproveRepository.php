<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 5:15 PM
 */
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class LeaveApproveRepository implements RepositoryInterface
{

    private $adapter;
    private $tableGateway;
    private $tableGatewayLeaveAssign;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LeaveApply::TABLE_NAME, $adapter);
        $this->tableGatewayLeaveAssign = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        // TODO: Implement add() method.
    }

    public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT L.LEAVE_ENAME,LA.NO_OF_DAYS,LA.START_DATE
                ,LA.END_DATE,LA.REQUESTED_DT AS APPLIED_DATE,
                LA.STATUS AS STATUS,
                LA.ID AS ID,
                E.FIRST_NAME,E.MIDDLE_NAME,E.LAST_NAME,
                LA.RECOMMENDED_BY AS RECOMMENDER,
                LA.APPROVED_BY AS APPROVER
                FROM HR_EMPLOYEE_LEAVE_REQUEST LA, 
                HR_LEAVE_MASTER_SETUP L,
                HR_EMPLOYEES E,
                HR_EMPLOYEES E1,
                HR_EMPLOYEES E2
                WHERE 
                L.LEAVE_ID=LA.LEAVE_ID AND
                E.EMPLOYEE_ID=LA.EMPLOYEE_ID AND
                E1.EMPLOYEE_ID=LA.RECOMMENDED_BY AND
                E2.EMPLOYEE_ID=LA.APPROVED_BY AND";
        if($status==null){
            $sql .=" ((LA.RECOMMENDED_BY=".$id." AND LA.STATUS='RQ') OR (LA.APPROVED_BY=".$id." AND LA.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " LA.STATUS='RC' AND
                LA.RECOMMENDED_BY=".$id;
        }else if($status=='AP'){
            $sql .= " LA.STATUS='AP' AND
                LA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" LA.STATUS='".$status."' AND
                ((LA.RECOMMENDED_BY=".$id." AND LA.RECOMMENDED_DT IS NOT NULL) OR (LA.APPROVED_BY=".$id.") )";
        }

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function edit(Model $model, $id)
    {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[LeaveApply::ID=>$id]);
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.START_DATE AS START_DATE"),
            new Expression("TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LA.ID AS ID"),
            new Expression("LA.END_DATE AS END_DATE"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.HALF_DAY AS HALF_DAY"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
        ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME']);

        $select->where([
            "LA.ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function assignedLeaveDetail($leaveId,$employeeId){
        $result =  $this->tableGatewayLeaveAssign->select(['EMPLOYEE_ID'=>$employeeId,'LEAVE_ID'=>$leaveId]);
        return $result->current();
    }

    public function updateLeaveBalance($leaveId,$employeeId,$balance){
        $this->tableGatewayLeaveAssign->update(["BALANCE"=>$balance],['LEAVE_ID'=>$leaveId,'EMPLOYEE_ID'=>$employeeId]);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}