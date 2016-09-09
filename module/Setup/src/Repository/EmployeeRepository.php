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
        $select->where(['STATUS'=>'E']);
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
        $rowset = $this->gateway->select(function (Select $select) use ($id) {
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

            $select->where(['EMPLOYEE_ID'=>$id]);
        });
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id)
    {
//        $this->gateway->update(['STATUS'=>'D','MODIFIED_DT'=>Helper::getcurrentExpressionDate()],['EMPLOYEE_ID' => $id]);
        $this->gateway->update(['STATUS'=>'D'],['EMPLOYEE_ID' => $id]);
    }

    public function edit(Model $model, $id)
    {
        $tempArray = $model->getArrayCopyForDB();

        if (array_key_exists('CREATED_DT',$tempArray)) {
            unset($tempArray['CREATED_DT']);
        }
        if (array_key_exists('EMPLOYEE_ID',$tempArray)) {
            unset($tempArray['EMPLOYEE_ID']);
        }
        if (array_key_exists('STATUS',$tempArray)) {
            unset($tempArray['STATUS']);
        }
       $this->gateway->update($tempArray, ['EMPLOYEE_ID' => $id]);

    }
}
