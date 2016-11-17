<?php

namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EmployeeRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HR_EMPLOYEES', $adapter);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from("HR_EMPLOYEES");
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
        $select->where(['STATUS' => 'E']);
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

    public function fetchById($id) {
        $rowset = $this->gateway->select(function (Select $select) use ($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), [
                        'birthDate',
                        'famSpouseBirthDate',
                        'famSpouseWeddingAnniversary',
                        'idDrivingLicenseExpiry',
                        'idCitizenshipIssueDate',
                        'idPassportExpiry',
                        'joinDate'
                    ]), false);

            $select->where(['EMPLOYEE_ID' => $id]);
        });
        return $rowset->current();
    }

    public function fetchForProfileById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['E' => HrEmployees::TABLE_NAME]);
        $select->columns([
            Helper::dateExpression(HrEmployees::BIRTH_DATE, "E"),
            Helper::columnExpression(HrEmployees::FIRST_NAME, "E"),
            Helper::columnExpression(HrEmployees::MIDDLE_NAME, "E"),
            Helper::columnExpression(HrEmployees::LAST_NAME, "E"),
            Helper::columnExpression(HrEmployees::GENDER_ID, "E"),
            Helper::columnExpression(HrEmployees::MOBILE_NO, "E"),
            Helper::dateExpression(HrEmployees::JOIN_DATE, "E"),
                ], true);
        $select->join(['B1' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B1." . Branch::BRANCH_ID, ['APPOINT_BRANCH' => 'BRANCH_NAME'], 'left')
                ->join(['B2' => Branch::TABLE_NAME], "E." . HrEmployees::CUR_BRANCH_ID . "=B2." . Branch::BRANCH_ID, ['CUR_BRANCH' => 'BRANCH_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['APPOINT_DEPARTMENT' => 'DEPARTMENT_NAME'], 'left')
                ->join(['D2' => Department::TABLE_NAME], "E." . HrEmployees::CUR_DEPARTMENT_ID . "=D2." . Department::DEPARTMENT_ID, ['CUR_DEPARTMENT' => 'DEPARTMENT_NAME'], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['APPOINT_DESIGNATION' => 'DESIGNATION_TITLE'], 'left')
                ->join(['DES2' => Designation::TABLE_NAME], "E." . HrEmployees::CUR_DESIGNATION_ID . "=DES2." . Designation::DESIGNATION_ID, ['CUR_DESIGNATION' => 'DESIGNATION_TITLE'], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::POSITION_ID . "=P1." . Position::POSITION_ID, ['APPOINT_POSITION' => 'POSITION_NAME'], 'left')
                ->join(['P2' => Position::TABLE_NAME], "E." . HrEmployees::CUR_POSITION_ID . "=P2." . Position::POSITION_ID, ['CUR_POSITION' => 'POSITION_NAME'], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['APPOINT_SERVICE_TYPE' => 'SERVICE_TYPE_NAME'], 'left')
                ->join(['S2' => ServiceType::TABLE_NAME], "E." . HrEmployees::CUR_SERVICE_TYPE_ID . "=S2." . ServiceType::SERVICE_TYPE_ID, ['CUR_SERVICE_TYPE' => 'SERVICE_TYPE_NAME'], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['APPOINT_SERVICE_EVENT_TYPE' => 'SERVICE_EVENT_TYPE_NAME'], 'left')
                ->join(['SE2' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::CUR_SERVICE_EVENT_TYPE_ID . "=SE2." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['CUR_SERVICE_EVENT_TYPE' => 'SERVICE_EVENT_TYPE_NAME'], 'left');

        $select->where(["E." . HrEmployees::EMPLOYEE_ID . "=$id"]);

        $statement = $sql->prepareStatementForSqlObject($select);
        // return $statement->getSql();
        $result = $statement->execute();

        return $result->current();
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
//        $this->gateway->update(['STATUS'=>'D','MODIFIED_DT'=>Helper::getcurrentExpressionDate()],['EMPLOYEE_ID' => $id]);
        $this->gateway->update(['STATUS' => 'D'], ['EMPLOYEE_ID' => $id]);
    }

    public function edit(Model $model, $id) {
        $tempArray = $model->getArrayCopyForDB();

        if (array_key_exists('CREATED_DT', $tempArray)) {
            unset($tempArray['CREATED_DT']);
        }
        if (array_key_exists('EMPLOYEE_ID', $tempArray)) {
            unset($tempArray['EMPLOYEE_ID']);
        }
        if (array_key_exists('STATUS', $tempArray)) {
            unset($tempArray['STATUS']);
        }
        $this->gateway->update($tempArray, ['EMPLOYEE_ID' => $id]);
    }

    public function branchEmpCount() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([Helper::columnExpression(HrEmployees::EMPLOYEE_ID, 'E', "COUNT"), HrEmployees::BRANCH_ID], true);
        $select->from(['E' => HrEmployees::TABLE_NAME]);
//        $select->join(["B" => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . " = B." . Branch::BRANCH_ID,[Branch::BRANCH_ID, Branch::BRANCH_NAME]);
        $select->group(["E." . HrEmployees::BRANCH_ID]);

        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql());
//        exit;
        return $statement->execute();
    }
    public function filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId,$serviceEventTypeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from("HR_EMPLOYEES");
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
        
        $select->where(['STATUS' => 'E']);
        
        if($emplyoeeId!=-1){
            $select->where([
                "EMPLOYEE_ID=".$emplyoeeId
            ]);
        }
        if($branchId!=-1){
            $select->where([
                "CUR_BRANCH_ID=".$branchId
            ]);
        }
        if($departmentId!=-1){
            $select->where([
                "CUR_DEPARTMENT_ID=".$departmentId
            ]);
        }
        if($designationId!=-1){
            $select->where([
                "CUR_DESIGNATION_ID=".$designationId
            ]);
        }
        if($positionId!=-1){
            $select->where([
                "CUR_POSITION_ID=".$positionId
            ]);
        }
        if($serviceTypeId!=-1){
            $select->where([
                "CUR_SERVICE_TYPE_ID=".$serviceTypeId
            ]);
        }
        if($serviceEventTypeId!=-1){
            $select->where([
                "CUR_SERVICE_EVENT_TYPE_ID=".$serviceEventTypeId
            ]);
        }
        
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

}
