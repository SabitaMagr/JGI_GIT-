<?php


namespace Setup\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class  EmployeeRepository implements RepositoryInterface
{
    private $gateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->gateway = new TableGateway('HR_EMPLOYEES', $adapter);
    }

    public function fetchAll()
    {
        return $this->gateway->select();
    }

    public function fetchById($id)
    {
        $rowset = $this->gateway->select(['EMPLOYEE_ID' => $id]);
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id)
    {

    }

    public function edit(Model $model, $id, $modifiedDt)
    {
        $tempArray=$model->getArrayCopyForDB();

        $this->gateway->update($tempArray, ['EMPLOYEE_ID' => $id]);

    }
}
