<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Repository;

use Application\Model\Model;
use System\Model\UserSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;


class LoginRepository implements RepositoryInterface {
    
    private $adapter;
    private $tableGateway;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway(UserSetup::TABLE_NAME,$adapter);
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
    
    public function fetchByUserName($userName){
        $where=['USER_NAME'=>$userName];
        $result=$this->tableGateway->select($where);
        return $result->current();
    }

    public function updateByUserName($userName){
        $set=['IS_LOCKED'=>'Y'];
        $where=['USER_NAME'=>$userName];
        $result=$this->tableGateway->update($set, $where);
    }
}
