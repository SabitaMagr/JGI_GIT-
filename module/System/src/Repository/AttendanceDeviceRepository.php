<?php

namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\AttendanceDevice;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;

class AttendanceDeviceRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AttendanceDevice::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([AttendanceDevice::STATUS => "D"], [AttendanceDevice::DEVICE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [AttendanceDevice::DEVICE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->where([AttendanceDevice::STATUS => 'E']);
                    $select->order(AttendanceDevice::DEVICE_NAME . " ASC");
                });
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select) use($id) {
            $select->where([AttendanceDevice::DEVICE_ID => $id]);
        });
        return $result->current();
    }

}
