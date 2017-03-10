<?php
namespace Asset\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Setup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class SetupRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway=new TableGateway(Setup::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
//                        echo '<pre>';
//                print_r($model);
//                echo '</pre>';
//                die();
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select){
            $select->where([Setup::STATUS=>'E']);
            $select->order(Setup::ASSET_EDESC." ASC");
        });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Setup::ASSET_ID => $id, Setup::STATUS => 'E']);
        return $result = $rowset->current();
    }

}