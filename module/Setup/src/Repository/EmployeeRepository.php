<?php

namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\EmployeeFile;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Setup\Model\Company;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\Gender;

class EmployeeRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;
    private $vdcGateway;
    private $districtGateway;
    private $zoneGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HRIS_EMPLOYEES', $adapter);
        $this->vdcGateway = new TableGateway('HRIS_VDC_MUNICIPALITIES', $adapter);
        $this->districtGateway = new TableGateway('HRIS_DISTRICTS', $adapter);
        $this->zoneGateway = new TableGateway('HRIS_ZONES', $adapter);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from("HRIS_EMPLOYEES");
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
        $select->where(['STATUS' => 'E', 'RETIRED_FLAG' => 'N', "JOIN_DATE <= SYSDATE"]);
        $select->order(['UPPER(FIRST_NAME)', 'UPPER(MIDDLE_NAME)', 'UPPER(LAST_NAME)']);

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

    public function fetchAllForAttendance() {
        $sql = "SELECT E.* FROM HRIS_EMPLOYEES E
        JOIN HRIS_EMPLOYEE_SHIFT_ASSIGN ESA ON (E.EMPLOYEE_ID=ESA.EMPLOYEE_ID) JOIN HRIS_SHIFTS S ON (ESA.SHIFT_ID=S.SHIFT_ID) 
        WHERE  
        (CASE
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'SUN' THEN ( CASE WHEN (S.WEEKDAY1 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'MON' THEN ( CASE WHEN (S.WEEKDAY2 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'TUE' THEN ( CASE WHEN (S.WEEKDAY3 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'WED' THEN ( CASE WHEN (S.WEEKDAY4 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'THU' THEN ( CASE WHEN (S.WEEKDAY5 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'FRI' THEN ( CASE WHEN (S.WEEKDAY6 = 'DAY_OFF') THEN 0 ELSE 1 END )
        WHEN  trim(TO_CHAR(SYSDATE, 'DY')) = 'SAT' THEN ( CASE WHEN (S.WEEKDAY7 = 'DAY_OFF') THEN 0 ELSE 1 END )
        END)=1 AND E.STATUS='E' AND E.RETIRED_FLAG='N' AND E.JOIN_DATE <= SYSDATE AND S.STATUS ='E' AND ESA.STATUS='E'
        ";
        $statement = $this->adapter->query($sql);
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

    public function getById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['E' => HrEmployees::TABLE_NAME]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), [
                    'birthDate',
                    'famSpouseBirthDate',
                    'famSpouseWeddingAnniversary',
                    'idDrivingLicenseExpiry',
                    'idCitizenshipIssueDate',
                    'idPassportExpiry',
                    'joinDate'
                        ], NULL, 'E'), false);

        $select
                ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME'], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME'], 'left')
                ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME'], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME'], 'left')
                ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME'], 'left')
//                ->join(['DT' => "HRIS_DISTRICTS"], "E." . HrEmployees::ID_CITIZENSHIP_ISSUE_PLACE . "=DT.DISTRICT_ID", ['ID_CIT_ISSUE_PLACE_NAME' => 'DISTRICT_NAME'], 'left')
//                ->join(['Z' => "HRIS_ZONES"], "E." . HrEmployees::ZON . "=Z.ZONE_ID", ['ZONE_NAME'], 'left')
//                ->join(['D' => "HRIS_DISTRICTS"], "E." . HrEmployees::DISTRICT_ID . "=D.DISTRICT_ID", ['DISTRICT_NAME'], 'left')
                ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME'], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE'], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P1." . Position::POSITION_ID, ['POSITION_NAME'], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['SERVICE_TYPE_NAME'], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME'], 'left');
        $select->where(["E." . HrEmployees::EMPLOYEE_ID . "=$id"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchForProfileById($id = null) {
        if (!isset($id) || $id == null || $id == "") {
            return [];
        }

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['E' => HrEmployees::TABLE_NAME]);
        $select->columns([
            Helper::dateExpression(HrEmployees::BIRTH_DATE, "E"),
            Helper::columnExpression(HrEmployees::FIRST_NAME, "E"),
            Helper::columnExpression(HrEmployees::MIDDLE_NAME, "E"),
            Helper::columnExpression(HrEmployees::PROFILE_PICTURE_ID, "E"),
            Helper::columnExpression(HrEmployees::LAST_NAME, "E"),
            Helper::columnExpression(HrEmployees::GENDER_ID, "E"),
            Helper::columnExpression(HrEmployees::MOBILE_NO, "E"),
            Helper::columnExpression(HrEmployees::MARITAL_STATUS, "E"),
            Helper::columnExpression(HrEmployees::EMPLOYEE_CODE, "E"),
            Helper::dateExpression(HrEmployees::JOIN_DATE, "E"),
                ], true);
        $select->join(['B1' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B1." . Branch::BRANCH_ID, ['BRANCH' => 'BRANCH_NAME'], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME'], 'left')
                ->join(['B2' => Branch::TABLE_NAME], "E." . HrEmployees::APP_BRANCH_ID . "=B2." . Branch::BRANCH_ID, ['APP_BRANCH' => 'BRANCH_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['DEPARTMENT' => 'DEPARTMENT_NAME'], 'left')
                ->join(['D2' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D2." . Department::DEPARTMENT_ID, ['APP_DEPARTMENT' => 'DEPARTMENT_NAME'], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['DESIGNATION' => 'DESIGNATION_TITLE'], 'left')
                ->join(['DES2' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES2." . Designation::DESIGNATION_ID, ['APP_DESIGNATION' => 'DESIGNATION_TITLE'], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::POSITION_ID . "=P1." . Position::POSITION_ID, ['POSITION' => 'POSITION_NAME'], 'left')
                ->join(['P2' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P2." . Position::POSITION_ID, ['APP_POSITION' => 'POSITION_NAME'], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['SERVICE_TYPE' => 'SERVICE_TYPE_NAME'], 'left')
                ->join(['S2' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S2." . ServiceType::SERVICE_TYPE_ID, ['APP_SERVICE_TYPE' => 'SERVICE_TYPE_NAME'], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE' => 'SERVICE_EVENT_TYPE_NAME'], 'left')
                ->join(['SE2' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE2." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['APP_SERVICE_EVENT_TYPE' => 'SERVICE_EVENT_TYPE_NAME'], 'left')
                ->join(['EF' => EmployeeFile::TABLE_NAME], "E." . HrEmployees::PROFILE_PICTURE_ID . "=EF." . EmployeeFile::FILE_CODE, ["FILE_NAME" => EmployeeFile::FILE_PATH], 'left');
        $select->where(["E." . HrEmployees::EMPLOYEE_ID . "=$id"]);

        $statement = $sql->prepareStatementForSqlObject($select);
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
        $select->where(['E.STATUS' => 'E', 'E.RETIRED_FLAG' => 'N']);

        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql());
//        exit;
        return $statement->execute();
    }

    public function filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $getResult = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["E" => "HRIS_EMPLOYEES"]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), [
                    'birthDate',
                    'famSpouseBirthDate',
                    'famSpouseWeddingAnniversary',
                    'idDrivingLicenseExpiry',
                    'idCitizenshipIssueDate',
                    'idPassportExpiry',
                    'joinDate'
                        ], NULL, 'E'), false);

        $select
                ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME'], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME'], 'left')
                ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME'], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME'], 'left')
                ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME'], 'left')
//                ->join(['DT' => "HRIS_DISTRICTS"], "E." . HrEmployees::ID_CITIZENSHIP_ISSUE_PLACE . "=DT.DISTRICT_ID", ['ID_CIT_ISSUE_PLACE_NAME' => 'DISTRICT_NAME'], 'left')
//                ->join(['Z' => "HRIS_ZONES"], "E." . HrEmployees::ZON . "=Z.ZONE_ID", ['ZONE_NAME'], 'left')
//                ->join(['D' => "HRIS_DISTRICTS"], "E." . HrEmployees::DISTRICT_ID . "=D.DISTRICT_ID", ['DISTRICT_NAME'], 'left')
                ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME'], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE'], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P1." . Position::POSITION_ID, ['POSITION_NAME'], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['SERVICE_TYPE_NAME'], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME'], 'left')
                ;

        $select->where(["E.STATUS='E'"]);

        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $select->where(["E.RETIRED_FLAG='Y'"]);
        } else {
            $select->where(["E.RETIRED_FLAG='N'"]);
        }

        if ($emplyoeeId != -1) {
            $select->where([
                "E.EMPLOYEE_ID=" . $emplyoeeId
            ]);
        }
        if ($branchId != -1) {
            $select->where([
                "E.BRANCH_ID=" . $branchId
            ]);
        }
        if ($departmentId != -1) {
            $select->where([
                "E.DEPARTMENT_ID=" . $departmentId
            ]);
        }
        if ($designationId != -1) {
            $select->where([
                "E.DESIGNATION_ID=" . $designationId
            ]);
        }
        if ($positionId != -1) {
            $select->where([
                "E.POSITION_ID=" . $positionId
            ]);
        }
        if ($serviceTypeId != -1) {
            $select->where([
                "E.SERVICE_TYPE_ID=" . $serviceTypeId
            ]);
        }
        if ($serviceEventTypeId != -1) {
            $select->where([
                "E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $result = $statement->execute();
        if ($getResult != null) {
            return $result;
        } else {
            $tempArray = [];
            foreach ($result as $item) {
                $tempObject = new HrEmployees();
                $tempObject->exchangeArrayFromDB($item);
                array_push($tempArray, $tempObject);
            }
            return $tempArray;
        }
    }

    public function getEmployeeListOfBirthday() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from("HRIS_EMPLOYEES");
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
        $select->where(["STATUS='E' AND RETIRED_FLAG='N'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $employeeList = [];

        foreach ($result as $row) {
            $today = date('d-M');
            $time = strtotime($row['BIRTH_DATE']);
            $date = date('d-M', $time);
            if ($date == $today) {
                array_push($employeeList, $row);
            }
        }
        return $employeeList;
    }

    public function getVdcMunicipalityDtl($id) {
        $result = $this->vdcGateway->select(['VDC_MUNICIPALITY_ID' => $id]);
        return $result->current();
    }

    public function getDistrictDtl($id) {
        $result = $this->districtGateway->select(['DISTRICT_ID' => $id]);
        return $result->current();
    }

    public function getZoneDtl($id) {
        $result = $this->zoneGateway->select(['ZONE_ID' => $id]);
        return $result->current();
    }

}
