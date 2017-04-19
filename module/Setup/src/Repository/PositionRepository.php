<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\Position;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;

class PositionRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Position::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Position::POSITION_ID]);
        unset($array[Position::CREATED_DT]);
        $this->tableGateway->update($array, [Position::POSITION_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Position::STATUS => 'D'], [Position::POSITION_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchActiveRecord() {
        $rowset = $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Position::class,[Position::POSITION_NAME]),false);
            $select->where([Position::STATUS => 'E']);
            $select->order(Position::POSITION_NAME . " ASC");
        });
        $result = [];
        $i = 1;
        foreach ($rowset as $row) {
            array_push($result, [
                'SN' => $i,
                'POSITION_ID' => $row['POSITION_ID'],
                'POSITION_NAME' => $row['POSITION_NAME'],
                'REMARKS' => $row['REMARKS']
            ]);
            $i += 1;
        }
        return $result;
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select([Position::POSITION_ID => $id]);
        return $row->current();
    }

}
