<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/30/16
 * Time: 12:20 PM
 */
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\RepositoryInterface;
use Application\Model\Model;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Model\LeaveApply;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveRequestRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LeaveApply::TABLE_NAME,$adapter);
        $this->tableGatewayLeaveAssign = new TableGateway(LeaveAssign::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        // TODO: Implement add() method.
    }

    public function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function selectAll($employeeId){

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(LA.START_DATE, 'DD-MON-YYYY') AS FROM_DATE"),
            new Expression("TO_CHAR(LA.END_DATE, 'DD-MON-YYYY') AS TO_DATE"),
            new Expression("LA.STATUS AS STATUS"),
        ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['L'=>'HR_LEAVE_MASTER_SETUP'],"L.LEAVE_ID=LA.LEAVE_ID",['LEAVE_CODE','LEAVE_ENAME']);

        $select->where([
            "L.STATUS='E'",
            "E.EMPLOYEE_ID=".$employeeId
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        // TODO: Implement fetchById() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}