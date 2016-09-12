<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:53 AM
 */

namespace LeaveManagement\Repository;


use Setup\Model\Model;
use Setup\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class LeaveApplyRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('HR_EMPLOYEE_LEAVE_REQUEST', $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id)
    {
        // TODO: Implement fetchById() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}