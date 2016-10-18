<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 3:00 PM
 */
namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use System\Model\RoleSetup;

class RoleSetupRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(RoleSetup::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[RoleSetup::ROLE_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([RoleSetup::STATUS=>"E"]);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([RoleSetup::ROLE_ID=>$id]);
        return $result->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([RoleSetup::STATUS=>"D"],[RoleSetup::ROLE_ID=>$id]);
    }
}