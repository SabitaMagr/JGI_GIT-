<?php


namespace Setup\Repository;

use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class  EmployeeRepository implements RepositoryInterface
{
    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HR_EMPLOYEES', $adapter);
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from("HR_EMPLOYEES");
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $tempArray = [];
        foreach ($result as $item) {
            $tempObject = new HrEmployees();
            $tempObject->exchangeArrayFromDB($item);

            array_push($tempArray, $tempObject);
        }
        return $tempArray;
    }

    public function fetchById($id)
    {
        $rowset = $this->gateway->select(function(Select $select){
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(),
            [
                'birthDate',
                'famSpouseBirthDate',
                'famSpouseWeddingAnniversary',
                'idDrivingLicenseExpiry',
                'idCitizenshipIssueDate',
                'idPassportExpiry',
                'joinDate'
            ]), false);
        });
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id)
    {
        $this->gateway->delete(['EMPLOYEE_ID' => $id]);
    }

    public function edit(Model $model, $id)
    {
        $tempArray = $model->getArrayCopyForDB();
        unset($tempArray['CREATED_DT']);
        unset($tempArray['EMPLOYEE_ID']);
        unset($tempArray['STATUS']);
        $this->gateway->update($tempArray, ['EMPLOYEE_ID' => $id]);

    }
}
