<?php

namespace SelfService\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Exception;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Model\HolidayBranch;
use Setup\Model\HolidayDesignation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class HolidayRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayBranch = new TableGateway(HolidayBranch::TABLE_NAME, $adapter);
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
        $today = Helper::getcurrentExpressionDate();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("H.HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("H.HOLIDAY_CODE AS HOLIDAY_CODE"),
            new Expression("INITCAP(H.HOLIDAY_ENAME) AS HOLIDAY_ENAME"),
            new Expression("H.HALFDAY AS HALFDAY"),
            new Expression("H.FISCAL_YEAR AS FISCAL_YEAR"),
            new Expression("H.REMARKS AS REMARKS"),
        ], true);

        $select->from(['H' => Holiday::TABLE_NAME])
                ->join(['HB' => HolidayBranch::TABLE_NAME], "HB.HOLIDAY_ID=H.HOLIDAY_ID", ['HOLIDAY_ID'], "left")
                ->join(['HD' => HolidayDesignation::TABLE_NAME], "H.HOLIDAY_ID=HD.HOLIDAY_ID", ['DESIGNATION_ID'], "left")
                ->join(['E' => 'HRIS_EMPLOYEES'], "E.BRANCH_ID=HB.BRANCH_ID AND E.DESIGNATION_ID=HD.DESIGNATION_ID", ['GENDER_ID'], "left");

        $select->where([
            "H.STATUS='E'",
            "E.EMPLOYEE_ID=" . $employeeId,
//           "H.END_DATE>=".$today->getExpression(),
            "((H.GENDER_ID IS NOT NULL AND H.GENDER_ID=E.GENDER_ID) OR H.GENDER_ID IS NULL)"
        ]);

        $select->order("H.START_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    function fetchById($id) {
        // TODO: Implement fetchById() method.
    }

}
