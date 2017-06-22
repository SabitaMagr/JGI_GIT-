<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\EmployeeFile;
use Setup\Model\Gender;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Model\ShiftSetup;
use Zend\Db\TableGateway\TableGateway;
use AttendanceManagement\Model\AttendanceDetail;
use Setup\Model\RecommendApprove;

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
        $colList = EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::BIRTH_DATE]);

        $select->columns($colList, false);
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
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FULL_NAME, HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [
                        HrEmployees::BIRTH_DATE,
                        HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                        HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                        HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                        HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                        HrEmployees::ID_PASSPORT_EXPIRY,
                        HrEmployees::JOIN_DATE
                    ]), false);
            $select->where(['EMPLOYEE_ID' => $id]);
        });
        return $rowset->current();
    }

    public function getById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $E = 'E';
        $fullNameExp = new Expression("CONCAT(CONCAT(CONCAT({$E}.FIRST_NAME,' '),CONCAT({$E}.MIDDLE_NAME, ' ')),{$E}.LAST_NAME) AS FULL_NAME");
        $columns = EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                    HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                    HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                    HrEmployees::ID_PASSPORT_EXPIRY,
                    HrEmployees::JOIN_DATE
                        ], NULL, NULL, NULL, $E);
        array_push($columns, $fullNameExp);
        $select->columns($columns, false);

        $select->from(['E' => HrEmployees::TABLE_NAME]);
        $select
                ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME' => new Expression('INITCAP(B.BRANCH_NAME)')], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME' => new Expression('INITCAP(C.COMPANY_NAME)')], 'left')
                ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME' => new Expression('INITCAP(G.GENDER_NAME)')], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME' => new Expression('INITCAP(RG.RELIGION_NAME)')], 'left')
                ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(CN.COUNTRY_NAME)')], 'left')
                ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME' => new Expression('INITCAP(VM.VDC_MUNICIPALITY_NAME)')], 'left')
                ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME' => new Expression('INITCAP(D1.DEPARTMENT_NAME)')], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE' => new Expression('INITCAP(DES1.DESIGNATION_TITLE)')], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P1." . Position::POSITION_ID, ['POSITION_NAME' => new Expression('INITCAP(P1.POSITION_NAME)')], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['SERVICE_TYPE_NAME' => new Expression('INITCAP(S1.SERVICE_TYPE_NAME)')], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(SE1.SERVICE_EVENT_TYPE_NAME)')], 'left');
        $select->where(["E." . HrEmployees::EMPLOYEE_ID . "=$id"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchForProfileById($id = null) {
        if (!isset($id) || $id == null || $id == "") {
            return [];
        }

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(
                EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [
                    HrEmployees::FIRST_NAME,
                    HrEmployees::MIDDLE_NAME,
                    HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                    HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                    HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                    HrEmployees::ID_PASSPORT_EXPIRY,
                    HrEmployees::JOIN_DATE
                        ], NULL, NULL, NULL, 'E'), false);

        $select->from(['E' => HrEmployees::TABLE_NAME]);
        $select->join(['B1' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B1." . Branch::BRANCH_ID, ['BRANCH' => 'BRANCH_NAME'], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME'], 'left')
                ->join(['F' => EmployeeFile::TABLE_NAME], "F." . EmployeeFile::FILE_CODE . "=C." . Company::LOGO, ['COMPANY_FILE_PATH' => "FILE_PATH", 'COMPANY_FILE_CODE' => "FILE_CODE", 'COMPANY_FILE_NAME' => "FILE_NAME"], 'left')
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
                ->join(['EF' => EmployeeFile::TABLE_NAME], "E." . HrEmployees::PROFILE_PICTURE_ID . "=EF." . EmployeeFile::FILE_CODE, ["FILE_NAME" => EmployeeFile::FILE_PATH], 'left')
                ->join(['RA' => RecommendApprove::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=RA." . RecommendApprove::EMPLOYEE_ID, ["RECOMMENDER_ID" => RecommendApprove::RECOMMEND_BY, "APPROVER_ID" => RecommendApprove::APPROVED_BY,], 'left')
                ->join(['E1' => HrEmployees::TABLE_NAME], "E1." . HrEmployees::EMPLOYEE_ID . "=RA." . RecommendApprove::RECOMMEND_BY, ["RECOMMENDER" => new Expression("INITCAP(E1.FULL_NAME)")], 'left')
                ->join(['E2' => HrEmployees::TABLE_NAME], "E2." . HrEmployees::EMPLOYEE_ID . "=RA." . RecommendApprove::APPROVED_BY, ["APPROVER" => new Expression("INITCAP(E2.FULL_NAME)")], 'left');
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
        return $statement->execute();
    }

    public function filterRecords($emplyoeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $getResult = null, $companyId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(
                EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [
                    HrEmployees::FULL_NAME,
                    HrEmployees::FIRST_NAME,
                    HrEmployees::MIDDLE_NAME,
                    HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                    HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                    HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                    HrEmployees::ID_PASSPORT_EXPIRY,
                    HrEmployees::JOIN_DATE
                        ], NULL, NULL, NULL, 'E'), false);

        $select->from(["E" => "HRIS_EMPLOYEES"]);

        $select
                ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME' => new Expression('INITCAP(B.BRANCH_NAME)')], 'left')
                ->join(['D' => Department::TABLE_NAME], "E." . HrEmployees::DEPARTMENT_ID . "=D." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME' => new Expression('INITCAP(D.DEPARTMENT_NAME)')], 'left')
                ->join(['DES' => Designation::TABLE_NAME], "E." . HrEmployees::DESIGNATION_ID . "=DES." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE' => new Expression('INITCAP(DES.DESIGNATION_TITLE)')], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME' => new Expression('INITCAP(C.COMPANY_NAME)')], 'left')
                ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME' => new Expression('INITCAP(G.GENDER_NAME)')], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME'], 'left')
                ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(CN.COUNTRY_NAME)')], 'left')
                ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME' => new Expression('INITCAP(VM.VDC_MUNICIPALITY_NAME)')], 'left')
                ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['APP_DEPARTMENT_NAME' => new Expression('INITCAP(D1.DEPARTMENT_NAME)')], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['APP_DESIGNATION_TITLE' => new Expression('INITCAP(DES1.DESIGNATION_TITLE)')], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P1." . Position::POSITION_ID, ['APP_POSITION_NAME' => new Expression('INITCAP(P1.POSITION_NAME)')], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['APP_SERVICE_TYPE_NAME' => new Expression('INITCAP(S1.SERVICE_TYPE_NAME)')], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['APP_SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(SE1.SERVICE_EVENT_TYPE_NAME)')], 'left')
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
        if ($companyId != null && $companyId != -1) {
            $select->where([
                "E.COMPANY_ID=" . $companyId
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
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE
                ]), false);

        $select->from("HRIS_EMPLOYEES");
//        $select->columns(Helper::convertColumnDateFormat($this->adapter, new HrEmployees(), ['birthDate']), false);
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

    public function fetchByEmployeeTypeWidShift($employeeType, $currentDate = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE
                        ], null, null, null, 'E'), false);
        $select->from(['E' => "HRIS_EMPLOYEES"])
                ->join(['SA' => ShiftAssign::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=SA." . ShiftAssign::EMPLOYEE_ID, ['EMPLOYEE_ID', 'SHIFT_ID'], 'left')
                ->join(['S' => ShiftSetup::TABLE_NAME], "SA." . ShiftAssign::SHIFT_ID . "=S." . ShiftSetup::SHIFT_ID, ['SHIFT_CODE',
                    'SHIFT_ENAME' => new Expression('INITCAP(S.SHIFT_ENAME)'),
                    'SHIFT_LNAME' => new Expression('INITCAP(S.SHIFT_LNAME)'),
                    'START_DATE' => new Expression("INITCAP(TO_CHAR(S.START_DATE, 'DD-MON-YYYY'))"),
                    'END_DATE' => new Expression("INITCAP(TO_CHAR(S.END_DATE, 'DD-MON-YYYY'))"),
                    'START_TIME' => new Expression("TO_CHAR(S.START_TIME, 'HH:MI AM')"),
                    'END_TIME' => new Expression("TO_CHAR(S.END_TIME, 'HH:MI AM')"),
                    'HALF_TIME' => new Expression("TO_CHAR(S.HALF_TIME, 'HH:MI AM')"),
                    'HALF_DAY_END_TIME' => new Expression("TO_CHAR(S.HALF_DAY_END_TIME, 'HH:MI AM')"),
                    'LATE_IN' => new Expression("TO_CHAR(S.LATE_IN, 'HH24:MI')"),
                    'EARLY_OUT' => new Expression("TO_CHAR(S.EARLY_OUT, 'HH24:MI')"),
                    'TOTAL_WORKING_HR' => new Expression("TO_CHAR(S.TOTAL_WORKING_HR, 'HH24:MI')"),
                    'ACTUAL_WORKING_HR' => new Expression("TO_CHAR(S.ACTUAL_WORKING_HR, 'HH24:MI')"),
                        ], 'left')
                ->join(['AD' => AttendanceDetail::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=AD." . AttendanceDetail::EMPLOYEE_ID, [
                    'IN_TIME' => new Expression("TO_CHAR(AD.IN_TIME, 'HH:MI AM')"),
                    'OUT_TIME' => new Expression("TO_CHAR(AD.OUT_TIME, 'HH:MI AM')"),
                        ], "left");
        if ($currentDate != null) {
            $startDate = " AND TO_DATE('" . $currentDate . "','DD-MON-YYYY') >= S.START_DATE AND TO_DATE('" . $currentDate . "','DD-MON-YYYY') <= S.END_DATE";
        } else {
            $startDate = "";
        }
        $select->where(["E.STATUS='E' AND E.RETIRED_FLAG='N' AND S.STATUS='E' AND SA.STATUS='E'", "E." . HrEmployees::EMPLOYEE_TYPE . "='" . $employeeType . "'" . $startDate]);
        $select->where([AttendanceDetail::ATTENDANCE_DT . "=TO_DATE('" . $currentDate . "','DD-MON-YYYY')"]);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

    public function fetchByAdminFlag() {
        $result = $this->gateway->select(["IS_ADMIN='Y'"]);
        return $result->current()->getArrayCopy();
    }

    public function fetchByAdminFlagList() {
        $result = $this->gateway->select(["IS_ADMIN='Y' AND STATUS='E'"]);
        return $result;
    }

    public function fetchEmployeeFullNameList() {
        $sql = "
            SELECT EMPLOYEE_ID AS EMPLOYEE_ID,
              INITCAP(CONCAT(CONCAT(CONCAT(LOWER(TRIM(FIRST_NAME)),' '),
              CASE
                WHEN MIDDLE_NAME IS NOT NULL
                THEN CONCAT(LOWER(TRIM(MIDDLE_NAME)), ' ')
                ELSE ''
              END ),LOWER(TRIM(LAST_NAME)))) AS FULL_NAME
            FROM HRIS_EMPLOYEES
            WHERE STATUS='E'
                ";
        $raw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($raw);
    }

}
