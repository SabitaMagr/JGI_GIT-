<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EmployeeManagerRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HRIS_EMPLOYEES', $adapter);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $colList = EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::BIRTH_DATE], NULL, NULL, NULL, 'E');
        $select->columns($colList, false);

        $select->from(['E' => HrEmployees::TABLE_NAME])
                ->join(['C' => "HRIS_COMPANY"], "E.COMPANY_ID=C.COMPANY_ID", ["COMPANY_NAME" => new Expression("INITCAP(C.COMPANY_NAME)")], "left")
                ->join(['B' => "HRIS_BRANCHES"], "E.BRANCH_ID=B.BRANCH_ID", ["BRANCH_NAME" => new Expression("INITCAP(B.BRANCH_NAME)")], "left")
                ->join(['D' => "HRIS_DEPARTMENTS"], "E.DEPARTMENT_ID=D.DEPARTMENT_ID", ["DEPARTMENT_NAME" => new Expression("INITCAP(D.DEPARTMENT_NAME)")], "left")
                ->join(['DE' => "HRIS_DESIGNATIONS"], "E.DESIGNATION_ID=DE.DESIGNATION_ID", ["DESIGNATION_TITLE" => new Expression("INITCAP(DE.DESIGNATION_TITLE)")], "left");

        $select->where(['E.STATUS' => 'E', 'E.RETIRED_FLAG' => 'N', "E.JOIN_DATE <= SYSDATE"]);
        $select->order(['UPPER(E.FULL_NAME)']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchById($id) {
        
    }

}
