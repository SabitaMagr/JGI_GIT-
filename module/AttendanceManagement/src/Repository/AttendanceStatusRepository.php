<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:10 PM
 */

namespace AttendanceManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\AttendanceRequestModel;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class AttendanceStatusRepository implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        // TODO: Implement add() method.
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function getAllRequest($status = null, $branchId = null, $employeeId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT"),
            new Expression("TO_CHAR(AR.APPROVED_DT, 'DD-MON-YYYY') AS APPROVED_DT"),
            new Expression("TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("AR.ID AS ID"),
            new Expression("TO_CHAR(AR.IN_TIME, 'HH:MI AM') AS IN_TIME"),
            new Expression("TO_CHAR(AR.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
            new Expression("AR.IN_REMARKS AS IN_REMARKS"),
            new Expression("AR.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AR.TOTAL_HOUR AS TOTAL_HOUR"),
                ], true);

        $select->from(['AR' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => "HR_EMPLOYEES"], "E.EMPLOYEE_ID=AR.EMPLOYEE_ID", ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'])
                ->join(['E1' => "HR_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.APPROVED_BY", ['FIRST_NAME1' => "FIRST_NAME", 'MIDDLE_NAME1' => "MIDDLE_NAME", 'LAST_NAME1' => "LAST_NAME"]);

        $select->where([
            "E.STATUS='E'"
        ]);
        if ($status != null) {
            $where = "AR.STATUS ='" . $status . "'";
            $select->where([$where]);
        }

        if ($branchId != null) {
            $select->where(["E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)"]);
        }

        if ($employeeId != null) {
            $select->where(["E." . HrEmployees::EMPLOYEE_ID . " = $employeeId"]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(
                [
            new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
            new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),
            new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("A.ID AS ID"),
            new Expression("A.IN_REMARKS AS IN_REMARKS"),
            new Expression("A.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TO_CHAR(A.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT")
                ], true);
        $select->from(['A' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'])
                ->join(['E1' => "HR_EMPLOYEES"], "E1.EMPLOYEE_ID=A.APPROVED_BY", ['FIRST_NAME1' => "FIRST_NAME", 'MIDDLE_NAME1' => "MIDDLE_NAME", 'LAST_NAME1' => "LAST_NAME"]);

        $select->where([AttendanceRequestModel::ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }
    public function getFilteredRecord($data){
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $attendanceRequestStatusId = $data['attendanceRequestStatusId'];
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT"),
            new Expression("TO_CHAR(AR.APPROVED_DT, 'DD-MON-YYYY') AS APPROVED_DT"),
            new Expression("TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("AR.ID AS ID"),
            new Expression("TO_CHAR(AR.IN_TIME, 'HH:MI AM') AS IN_TIME"),
            new Expression("TO_CHAR(AR.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
            new Expression("AR.IN_REMARKS AS IN_REMARKS"),
            new Expression("AR.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AR.TOTAL_HOUR AS TOTAL_HOUR"),
                ], true);

        $select->from(['AR' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => "HR_EMPLOYEES"], "E.EMPLOYEE_ID=AR.EMPLOYEE_ID", ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'])
                ->join(['E1' => "HR_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.APPROVED_BY", ['FIRST_NAME1' => "FIRST_NAME", 'MIDDLE_NAME1' => "MIDDLE_NAME", 'LAST_NAME1' => "LAST_NAME"]);
        
        $select->where([
            "E.STATUS='E'"
        ]);
        
        if ($attendanceRequestStatusId != -1) {
            $select->where([
                "AR.STATUS ='" . $attendanceRequestStatusId . "'"
            ]);          
        }
       
        if($fromDate!=null){
            $select->where([
                "AR.ATTENDANCE_DT>=TO_DATE('".$fromDate."','DD-MM-YYYY')"
            ]);
        }
        
        if($toDate!=null){   
            $select->where([
                "AR.ATTENDANCE_DT<=TO_DATE('".$toDate."','DD-MM-YYYY')"
            ]);
        }

        if ($employeeId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " = $employeeId"
            ]);
        }
        
        if ($branchId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)"
            ]);
        }
        if ($departmentId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DEPARTMENT_ID . "= $departmentId)"
            ]);
            
        }
        if ($designationId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DESIGNATION_ID . "= $designationId)"
            ]);            
        }
        if ($positionId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::POSITION_ID . "= $positionId)"
            ]);           
        }
        if ($serviceTypeId != -1) {
            $select->where([
                "E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_TYPE_ID . "= $serviceTypeId)"
            ]);            
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}
