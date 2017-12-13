<?php

namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    function add(Model $model) {
        // TODO: Implement add() method.
    }

    function edit(Model $model, $id) {
        // TODO: Implement edit() method.
    }

    function delete($id) {
        // TODO: Implement delete() method.
    }

    function fetchAll() {
        
    }

    function selectAll($employeeId) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.BALANCE AS BALANCE"),
            new Expression("(LA.TOTAL_DAYS - LA.BALANCE) AS LEAVE_TAKEN"),
                ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")]);

        $select->where([
            "L.STATUS" => 'E',
            "LA.EMPLOYEE_ID" => $employeeId
        ]);
        $select->order("L.LEAVE_ENAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    function fetchById($id) {
        // TODO: Implement fetchById() method.
    }

}
