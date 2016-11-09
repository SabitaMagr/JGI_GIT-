<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/4/16
 * Time: 10:28 AM
 */

namespace Payroll\Repository;


use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\PayPositionSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class PayPositionRepo implements RepositoryInterface
{
    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(PayPositionSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
    }

    public function fetchAll()
    {
    }

    public function fetchById($id)
    {
        return $this->gateway->select([PayPositionSetup::PAY_ID => $id]);
    }

    public function fetchByPositionId($id)
    {
        return $this->gateway->select([PayPositionSetup::POSITION_ID => $id]);
    }

    public function delete($id)
    {
        return $this->gateway->delete([PayPositionSetup::PAY_ID => $id[0], PayPositionSetup::POSITION_ID => $id[1]]);
    }
}