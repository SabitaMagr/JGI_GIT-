<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/13/16
 * Time: 3:14 PM
 */
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\EmployeeQualification;
use Zend\Db\TableGateway\TableGateway;

class EmployeeQualificationRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmployeeQualification::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[EmployeeQualification::ID=>$id]);

    }

    public function fetchAll()
    {
        return $this->tableGateway->select([EmployeeQualification::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([EmployeeQualification::ID=>$id]);
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId){
        return $this->tableGateway->select([EmployeeQualification::EMPLOYEE_ID=>$employeeId,EmployeeQualification::STATUS=>'E']);
    }

    public function delete($id)
    {
        $this->tableGateway->update([EmployeeQualification::STATUS=>'D'],[EmployeeQualification::ID=>$id]);
    }
}