<?php


namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class  EmployeeRepository implements RepositoryInterface
{
    private $gateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->gateway = new TableGateway('employee', $adapter);
    }

    public function fetchAll()
    {
        return $this->gateway->select();
    }

    public function fetchById($id)
    {
        $rowset = $this->gateway->select(['employeeCode' => $id]);
        return $rowset->current();
    }



    public function add(ModelInterface $model)
    {
        $this->gateway->insert($model->getArrayCopy());

    }

    public function edit(ModelInterface $model, $id)
    {
        $this->gateway->update($model->getArrayCopy(), ['employeeCode' => $id]);
    }

    public function delete($id)
    {

    }
}
