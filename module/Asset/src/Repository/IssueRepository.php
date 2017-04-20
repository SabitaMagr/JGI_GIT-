<?php

namespace Asset\Repository;

use Application\Model\Model;
use Application\Helper\EntityHelper;
use Application\Repository\RepositoryInterface;
use Asset\Model\Issue;
use Asset\Model\Setup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class IssueRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Issue::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAssetRemBalance($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
//        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Setup::class, [Setup::ASSET_EDESC, Setup::ASSET_NDESC]),false);
//        $select->columns([
//            new Expression("A.ASSET_ID AS ASSET_ID"),
//            new Expression("A.ASSET_CODE AS ASSET_CODE"),
//            new Expression("A.ASSET_EDESC AS ASSET_EDESC"),
//            new Expression("A.BRAND_NAME AS BRAND_NAME"),
//            new Expression("A.MODEL_NO AS MODEL_NO"),
//            new Expression("A.QUANTITY AS QUANTITY")
//                ], true);
        $select->from(['A' => Setup::TABLE_NAME]);
//                ->join(['AG' => Group::TABLE_NAME], 'A.' . Setup::ASSET_GROUP_ID . '=AG.' . Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], "left");

        $select->where(["A." . Setup::ASSET_ID . "='" . $id . "'"]);
        $select->where(["A." . Setup::STATUS . "='E'"]);
//        $select->order("A." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql());
//        die();
//        die();SELECT A.* FROM HRIS_ASSET_SETUP A
        $result = $statement->execute();

        return $result->current();

    }

}
