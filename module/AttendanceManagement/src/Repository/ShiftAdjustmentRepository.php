<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftAdjustmentModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of ShiftAdjustmentRepository
 *
 * @author root
 */
class ShiftAdjustmentRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ShiftAdjustmentModel::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
          $array = $model->getArrayCopyForDB();
        unset($array[ShiftAdjustmentModel::ADJUSTMENT_ID]);
        unset($array[ShiftAdjustmentModel::CREATED_DT]);
        $this->tableGateway->update($array, [ShiftAdjustmentModel::ADJUSTMENT_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['SA' => ShiftAdjustmentModel::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftAdjustmentModel::class, NULL, [
                    ShiftAdjustmentModel::ADJUSTMENT_START_DATE,
                    ShiftAdjustmentModel::ADJUSTMENT_END_DATE
                        ], [
                    ShiftAdjustmentModel::START_TIME,
                    ShiftAdjustmentModel::END_TIME
                        ], NULL, NULL, 'SA', FALSE, FALSE, NULL), false);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(ShiftAdjustmentModel::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftAdjustmentModel::class, NULL, [
                    ShiftAdjustmentModel::ADJUSTMENT_START_DATE,
                    ShiftAdjustmentModel::ADJUSTMENT_END_DATE
                        ], [
                    ShiftAdjustmentModel::START_TIME,
                    ShiftAdjustmentModel::END_TIME
                        ], NULL, NULL,NULL, FALSE, FALSE, NULL), false);
        $select->where([ShiftAdjustmentModel::ADJUSTMENT_ID . '=' . $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
