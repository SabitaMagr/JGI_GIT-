<?php
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\RepositoryInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use SelfService\Model\LeaveSubstitute;

class LeaveSubstituteRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveSubstitute::TABLE_NAME,$adapter);
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
        
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([LeaveSubstitute::LEAVE_REQUEST_ID=>$id]);
        return $result->current();
    }
}
