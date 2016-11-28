<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/3/16
 * Time: 1:36 PM
 */

namespace Payroll\Repository;


use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValue;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class FlatValueRepository implements RepositoryInterface
{
    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway(FlatValue::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        return $this->tableGateway->update($model->getArrayCopyForDB(),[FlatValue::FLAT_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([FlatValue::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        return $this->tableGateway->select([FlatValue::FLAT_ID=>$id])->current();
    }

    public function delete($id)
    {
        return $this->tableGateway->update([FlatValue::STATUS=>'D'],[FlatValue::FLAT_ID=>$id]);
    }
}