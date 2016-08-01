<?php


namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;

class  EmployeeRepository implements EmployeeRepositoryInterface
{
    private $gateway;

    public function __construct(TableGateway $gateway)
    {
        $this->gateway = $gateway;
    }


    public function addEmployee(Employee $employee)
    {
       echo  $this->gateway->insert($employee->getArrayCopy());
        die();
    }

    public function editEmployee(Employee $employee)
    {
        $this->gateway->update($employee->getArrayCopy(), ['employeeCode' => $employee->employeeCode]);
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

    public function deleteEmployee(Employee $employee)
    {

    }
}