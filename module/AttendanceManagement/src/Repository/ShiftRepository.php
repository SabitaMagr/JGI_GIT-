<?php

namespace AttendanceManagement\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ShiftRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ShiftSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[ShiftSetup::SHIFT_ID]);
        unset($array[ShiftSetup::CREATED_DT]);
        unset($array[ShiftSetup::STATUS]);
        $this->tableGateway->update($array, [ShiftSetup::SHIFT_ID => $id]);
    }

    public function fetchAll() {
//        return $this->tableGateway->select();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(ShiftSetup::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new ShiftSetup(), ['startDate', 'endDate'], ['startTime', 'endTime']), false);
        $select->where([ShiftSetup::STATUS => 'E']);
        $select->order(ShiftSetup::SHIFT_ENAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(ShiftSetup::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new ShiftSetup(), ['startDate', 'endDate'], ['startTime', 'endTime', 'halfTime', 'halfDayEndTime'], null, ['lateIn', 'earlyOut', 'totalWorkingHr', 'actualWorkingHr']), false);
        $select->where([ShiftSetup::SHIFT_ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result->current();
    }

    public function fetchActiveRecord() {
        return $rowset = $this->tableGateway->select(function(Select $select) {
            $select->where([ShiftSetup::STATUS => 'E']);
            $select->order(ShiftSetup::SHIFT_ENAME . " ASC");
        });
    }

    public function delete($id) {
        $this->tableGateway->update([ShiftSetup::STATUS => 'D'], [ShiftSetup::SHIFT_ID => $id]);
    }

}
