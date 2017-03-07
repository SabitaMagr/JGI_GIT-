<?php

namespace Asset\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Group;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class GroupRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Group::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
         $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Group::STATUS=>'D'],[Group::ASSET_GROUP_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        
    }

//    public function fetchAll() {
//        return $this->tableGateway->select();
//    }
    
    public function fetchAll() {
         return $this->tableGateway->select(function(Select $select){
            $select->where([Group::STATUS=>'E']);
            $select->order(Group::ASSET_GROUP_EDESC." ASC");
        });
    }

    public function fetchById($id) {
          $rowset = $this->tableGateway->select([Group::ASSET_GROUP_ID => $id, Group::STATUS => 'E']);
        return $result = $rowset->current();
    }

}
