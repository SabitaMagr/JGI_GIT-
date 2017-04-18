<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
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
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Company::COMPANY_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->where([Company::STATUS => EntityHelper::STATUS_ENABLED]);
                    $select->order([Company::COMPANY_NAME => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([
            Company::COMPANY_ID => $id,
            Company::STATUS => EntityHelper::STATUS_ENABLED
        ]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([
            Company::STATUS => EntityHelper::STATUS_DISABLED
                ], [
            Company::COMPANY_ID => $id
        ]);
    }
}
