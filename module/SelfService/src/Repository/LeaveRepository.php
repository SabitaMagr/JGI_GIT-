<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/30/16
 * Time: 11:20 AM
 */
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveRepository implements RepositoryInterface
{
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    function add(Model $model)
    {
        // TODO: Implement add() method.
    }
    function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }
    function delete($id)
    {
        // TODO: Implement delete() method.
    }
    function fetchAll()
    {

    }
    function selectAll($employeeId){

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.BALANCE AS BALANCE"),
        ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['L'=>'HR_LEAVE_MASTER_SETUP'],"L.LEAVE_ID=LA.LEAVE_ID",['LEAVE_CODE','LEAVE_ENAME']);

        $select->where([
            "L.STATUS='E'",
            "E.EMPLOYEE_ID=".$employeeId
        ]);
        $select->order("L.LEAVE_ENAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    function fetchById($id)
    {
        // TODO: Implement fetchById() method.

    }
}