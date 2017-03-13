<?php

namespace Asset\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Group;
use Asset\Model\Setup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class SetupRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Setup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
//                        echo '<pre>';
//                print_r($model);
//                echo '</pre>';
//                die();
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
         $this->tableGateway->update([Setup::STATUS=>'D'],[Setup::ASSET_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Setup::ASSET_ID]);
        unset($data[Setup::CREATED_DATE]);
        unset($data[Setup::STATUS]);
        $this->tableGateway->update($data, [Setup::ASSET_ID => $id]);
    }

    public function fetchAll() {
//        return $this->tableGateway->select(function(Select $select){
//            $select->where([Setup::STATUS=>'E']);
//            $select->order(Setup::ASSET_EDESC." ASC");
//        });


        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("A.ASSET_ID AS ASSET_ID"),
            new Expression("A.ASSET_CODE AS ASSET_CODE"),
            new Expression("A.ASSET_EDESC AS ASSET_EDESC"),
            new Expression("A.BRAND_NAME AS BRAND_NAME"),
            new Expression("A.MODEL_NO AS MODEL_NO"),
            new Expression("A.QUANTITY AS QUANTITY")
                ], true);
        $select->from(['A' => Setup::TABLE_NAME])
                ->join(['AG' => Group::TABLE_NAME], 'A.' . Setup::ASSET_GROUP_ID . '=AG.' . Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], "left");

        $select->where(["A." . Setup::STATUS . "='E'"]);
        $select->order("A." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql());
//        die();
        $result = $statement->execute();
//        print_r($result);
//        die();
        return $result;
    }

    public function fetchById($id) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['E' => Setup::TABLE_NAME]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Setup(), [
                    'purchaseDate',
                    'expiaryDate'
                        ], NULL, 'E'), false);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
