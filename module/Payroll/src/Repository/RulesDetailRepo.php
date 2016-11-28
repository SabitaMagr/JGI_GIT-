<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/20/16
 * Time: 11:11 AM
 */

namespace Payroll\Repository;


use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\RulesDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class RulesDetailRepo implements RepositoryInterface
{
    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(RulesDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
       return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        return $this->gateway->update($model->getArrayCopyForDB(),[RulesDetail::PAY_ID=>$id]);
    }

    public function fetchAll()
    {
    }

    public function fetchById($id)
    {
        return $this->gateway->select([RulesDetail::PAY_ID=>$id])->current();
    }

    public function delete($id)
    {
    }
}