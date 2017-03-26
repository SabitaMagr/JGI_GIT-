<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
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
        $this->tableGateway->update($temp, [Department::DEPARTMENT_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['D' => Department::TABLE_NAME]);
        $select->join(['C' => "HRIS_COUNTRIES"], "D." . Department::COUNTRY_ID . "=C.COUNTRY_ID", ['COUNTRY_NAME'], 'left')
                ->join(['PD' => Department::TABLE_NAME], "D." . Department::PARENT_DEPARTMENT . "=PD.DEPARTMENT_ID", ['PARENT_DEPARTMENT' => 'DEPARTMENT_NAME'], 'left');
        $select->where(["D.STATUS='E'"]);
        $select->order("D." . Department::DEPARTMENT_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([Department::DEPARTMENT_ID => $id]);
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
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=B." . Branch::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME'), 'inner');
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
//        echo '<pre>';
//        print_r($companyList);
//        die();
        return $companyList;
    }

}
