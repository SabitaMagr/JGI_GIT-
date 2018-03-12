<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustomerContractDetailModel;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CustomerContractDetailRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustomerContractDetailModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContractDetailModel::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAllContractDetailByContractId($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerContractDetailModel::class, NULL, NULL, NULL, NULL, NULL, "CD"
                ), false);
        $select->from(["CD" => CustomerContractDetailModel::TABLE_NAME]);
        $select->join(["D" => Designation::TABLE_NAME], "CD." . CustomerContractDetailModel::DESIGNATION_ID . "=D." . Designation::DESIGNATION_ID, ["DESIGNATION_TITLE" => new Expression('INITCAP(D.DESIGNATION_TITLE)')], "left"
        );
        $select->where(["CD.".CustomerContractDetailModel::STATUS => 'E']);
        $select->where(["CD.".CustomerContractDetailModel::CONTRACT_ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
