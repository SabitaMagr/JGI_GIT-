<?php
namespace Appraisal\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\DefaultRating;
use Zend\Authentication\AuthenticationService;

class DefaultRatingRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    private $loggedInEmployeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(DefaultRating::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedInEmployeeId = $auth->getStorage()->read()['employee_id'];
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

}


