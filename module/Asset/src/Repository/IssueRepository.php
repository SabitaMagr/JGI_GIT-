<?php

namespace Asset\Repository;

use Application\Model\Model;
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
    
    
    public function fetchallIssuableAsset(){
        
        $sql = "SELECT * FROM HRIS_ASSET_SETUP WHERE QUANTITY_BALANCE>0 AND STATUS='E' ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $list = [];
        $list = [];
        foreach ($result as $row) {
            $list['A'][$row['ASSET_ID']]=$row;
            $list['B'][$row['ASSET_ID']]=$row['ASSET_EDESC'];
        }
        return $list;
    
        
        
    }

    public function fetchAssetRemBalance($id) {
        $sql = "SELECT * FROM HRIS_ASSET_SETUP ";
        $sql.="WHERE ASSET_ID='$id'";
        $statement = $this->adapter->query($sql);   
        $result = $statement->execute();
        return $result->current();
    }

}
