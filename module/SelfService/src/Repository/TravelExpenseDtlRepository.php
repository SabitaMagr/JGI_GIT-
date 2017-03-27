<?php
namespace SelfService\Repository;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Authentication\AuthenticationService;

class TravelExpenseDtlRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    private $loggedInEmployee;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TravelExpenseDetail::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedInEmployee = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
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
