<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class DepartmentRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Department::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        if (!isset($temp['PARENT_DEPARTMENT'])) {
            $temp['PARENT_DEPARTMENT'] = NULL;
        }
        $this->tableGateway->update($temp, [Department::DEPARTMENT_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Department::class, [Department::DEPARTMENT_NAME], NULL, NULL, NULL, NULL, 'D'), false);


        $select->from(['D' => Department::TABLE_NAME]);
        $select->join(['C' => "HRIS_COUNTRIES"], "D." . Department::COUNTRY_ID . "=C.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(C.COUNTRY_NAME)')], 'left')
                ->join(['PD' => Department::TABLE_NAME], "D." . Department::PARENT_DEPARTMENT . "=PD.DEPARTMENT_ID", ['PARENT_DEPARTMENT' => new Expression('INITCAP(PD.DEPARTMENT_NAME)')], 'left')
                ->join(['B' => Branch::TABLE_NAME], "D." . Department::BRANCH_ID . "=B." . Branch::BRANCH_ID, [Branch::BRANCH_NAME => new Expression('INITCAP(B.' . Branch::BRANCH_NAME . ')')], 'left')
                ->join(['CP' => Company::TABLE_NAME], "CP." . Company::COMPANY_ID . "=D." . Department::COMPANY_ID, [Company::COMPANY_NAME => new Expression('INITCAP(CP.COMPANY_NAME)')], 'left');
        $select->where(["D.STATUS='E'"]);
        $select->order([
            "D." . Department::DEPARTMENT_NAME => Select::ORDER_ASCENDING,
            'CP.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING,
            'B.' . Branch::BRANCH_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Department::class, [Department::DEPARTMENT_NAME]), false);
            $select->where([Department::DEPARTMENT_ID => $id]);
        });

        return $result->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Department::STATUS => 'D'], [Department::DEPARTMENT_ID => $id]);
    }

    public function fetchAllBranchAndCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['BRANCH_ID', 'BRANCH_NAME']);
        $select->from(['B' => Branch::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=B." . Branch::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME' => new Expression('INITCAP(C.COMPANY_NAME)')), 'inner');
        $select->where(["C.STATUS='E'"]);
        $select->where(["B.STATUS='E'"]);
        $select->order("B." . Branch::BRANCH_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }

        $companyList = [];
        foreach ($list as $val) {
            $newKey = $val['COMPANY_ID'];
            $companyList[$newKey][] = $val;
        }
        return $companyList;
    }

    public function fetchAllBranchAndDepartment() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['DEPARTMENT_ID', 'DEPARTMENT_NAME']);
        $select->from(['D' => Department::TABLE_NAME]);
        $select->join(['B' => Branch::TABLE_NAME], "B." . Branch::BRANCH_ID . "=D." . Department::BRANCH_ID, array('BRANCH_ID', 'BRANCH_NAME' => new Expression('B.BRANCH_NAME')), 'inner');
        $select->where(["B.STATUS='E'"]);
        $select->where(["D.STATUS='E'"]);
        $select->order("D." . Department::DEPARTMENT_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        $departmentList = [];
        foreach ($list as $val) {
            $newKey = $val['BRANCH_ID'];
            $departmentList[$newKey][] = $val;
        }
        return $departmentList;
    }

}
