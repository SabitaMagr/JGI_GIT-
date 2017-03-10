<?php
namespace Asset\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Setup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;


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
        
    }

    public function fetchById($id) {
        
    }

}