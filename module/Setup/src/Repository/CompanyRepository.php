<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CompanyRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Company::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
//        print "<pre>";
//        print_r($model);
//        exit;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Company::COMPANY_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->where([Company::STATUS => 'E']);
                    $select->order(Company::COMPANY_NAME . " ASC");
                });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Company::COMPANY_ID => $id, Company::STATUS => 'E']);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Company::STATUS => 'D'], [Company::COMPANY_ID => $id]);
    }

    public function fetchAllDesignationAndCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['DESIGNATION_ID', 'DESIGNATION_TITLE']);
        $select->from(['D' => Designation::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=D." . Designation::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME'), 'inner');
        $select->where(["C.STATUS='E'"]);
        $select->where(["D.STATUS='E'"]);
        $select->order("D." . Designation::DESIGNATION_TITLE . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }

        $designationList = [];
        foreach ($list as $val) {
            $newKey = $val['COMPANY_ID'];
            $designationList[$newKey][] = $val;
        }
        return $designationList;
    }

}
