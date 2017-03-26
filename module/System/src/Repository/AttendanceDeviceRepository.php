<?php

namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\AttendanceDevice;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class AttendanceDeviceRepository implements RepositoryInterface {
    
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(AttendanceDevice::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) { 
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
         return $this->tableGateway->select(function(Select $select){
//            $select->where([AttendanceDevice::ISACTIVE=>'Y']);
            $select->order(AttendanceDevice::DEVICE_NAME." ASC");
        });
    }

    public function fetchById($id) {
        
    }

}
