<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:10 PM
 */

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
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
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceRequestModel::class, NULL, [
                    AttendanceRequestModel::REQUESTED_DT,
                    AttendanceRequestModel::APPROVED_DT,
                    AttendanceRequestModel::ATTENDANCE_DT
                        ], [
                    AttendanceRequestModel::IN_TIME,
                    AttendanceRequestModel::OUT_TIME
                        ], NULL, NULL, 'AR'), false);

//        $select->columns([
//            new Expression("TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT"),
//            new Expression("TO_CHAR(AR.APPROVED_DT, 'DD-MON-YYYY') AS APPROVED_DT"),
//            new Expression("TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
//            new Expression("AR.STATUS AS STATUS"),
//            new Expression("AR.ID AS ID"),
//            new Expression("TO_CHAR(AR.IN_TIME, 'HH:MI AM') AS IN_TIME"),
//            new Expression("TO_CHAR(AR.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
//            new Expression("AR.IN_REMARKS AS IN_REMARKS"),
//            new Expression("AR.OUT_REMARKS AS OUT_REMARKS"),
//            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
//            new Expression("AR.TOTAL_HOUR AS TOTAL_HOUR"),
//                ], true);

        $select->from(['AR' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=AR.EMPLOYEE_ID", ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)')], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.APPROVED_BY", ['FIRST_NAME1' => new Expression('INITCAP(E1.FIRST_NAME)'), 'MIDDLE_NAME1' => new Expression('INITCAP(E1.MIDDLE_NAME)'), 'LAST_NAME1' => new Expression('INITCAP(E1.LAST_NAME)')], "left");

        $select->where([
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N'"
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
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AttendanceRequestModel::class, NULL, [
                    AttendanceRequestModel::REQUESTED_DT,
                    AttendanceRequestModel::ATTENDANCE_DT
                        ], [
                    AttendanceRequestModel::IN_TIME,
                    AttendanceRequestModel::OUT_TIME
                        ], NULL, NULL, 'A'), false);

//        $select->columns(
//                [
//            new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
//            new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),
//            new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
//            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
//            new Expression("A.ID AS ID"),
//            new Expression("A.IN_REMARKS AS IN_REMARKS"),
//            new Expression("A.OUT_REMARKS AS OUT_REMARKS"),
//            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR"),
//            new Expression("A.STATUS AS STATUS"),
//            new Expression("A.APPROVED_REMARKS AS APPROVED_REMARKS"),
//            new Expression("TO_CHAR(A.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT")
//                ], true);
        $select->from(['A' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)')], "left")
//                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=A.APPROVED_BY", ['FIRST_NAME1' => "FIRST_NAME", 'MIDDLE_NAME1' => "MIDDLE_NAME", 'LAST_NAME1' => "LAST_NAME"],"left");
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.APPROVED_BY", ['FIRST_NAME1' => new Expression('INITCAP(E1.FIRST_NAME)'), 'MIDDLE_NAME1' => new Expression('INITCAP(E1.MIDDLE_NAME)'), 'LAST_NAME1' => new Expression('INITCAP(E1.LAST_NAME)')], "left");

        $select->where([AttendanceRequestModel::ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

    public function getFilteredRecord($data, $recomApproveId = null) {
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $attendanceRequestStatusId = $data['attendanceRequestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];

        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        } else {
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }

        $sql = "SELECT AR.ID                                           AS ID,
                  AR.EMPLOYEE_ID                                       AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY'))    AS ATTENDANCE_DT,
                  BS_DATE(TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY'))    AS ATTENDANCE_DT_N,
                  INITCAP(TO_CHAR(AR.IN_TIME, 'HH:MI AM'))             AS IN_TIME,
                  INITCAP(TO_CHAR(AR.OUT_TIME, 'HH:MI AM'))            AS OUT_TIME,
                  AR.IN_REMARKS                                        AS IN_REMARKS,
                  AR.OUT_REMARKS                                       AS OUT_REMARKS,
                  AR.TOTAL_HOUR                                        AS TOTAL_HOUR,
                  AR.STATUS                                            AS STATUS,
                  AR.APPROVED_BY                                       AS APPROVED_BY,
                  INITCAP(TO_CHAR(AR.APPROVED_DT, 'DD-MON-YYYY'))      AS APPROVED_DT,
                  INITCAP(TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY'))     AS REQUESTED_DT,
                  BS_DATE(TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY'))     AS REQUESTED_DT_N,
                  AR.APPROVED_REMARKS                                  AS APPROVED_REMARKS,
                  AR.RECOMMENDED_BY                                    AS RECOMMENDED_BY,
                  AR.RECOMMENDED_REMARKS                               AS RECOMMENDED_REMARKS,
                  INITCAP(TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                  INITCAP(E.FIRST_NAME)                                AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME)                               AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)                                 AS LAST_NAME,
                  INITCAP(E.FULL_NAME)                                 AS FULL_NAME,
                  INITCAP(E1.FIRST_NAME)                               AS FN1,
                  INITCAP(E1.MIDDLE_NAME)                              AS MN1,
                  INITCAP(E1.LAST_NAME)                                AS LN1,
                  INITCAP(E2.FIRST_NAME)                               AS FN2,
                  INITCAP(E2.MIDDLE_NAME)                              AS MN2,
                  INITCAP(E2.LAST_NAME)                                AS LN2,
                  RA.RECOMMEND_BY                                      AS RECOMMENDER,
                  RA.APPROVED_BY                                       AS APPROVER,
                  INITCAP(RECM.FIRST_NAME)                             AS RECM_FN,
                  INITCAP(RECM.MIDDLE_NAME)                            AS RECM_MN,
                  INITCAP(RECM.LAST_NAME)                              AS RECM_LN,
                  INITCAP(APRV.FIRST_NAME)                             AS APRV_FN,
                  INITCAP(APRV.MIDDLE_NAME)                            AS APRV_MN,
                  INITCAP(APRV.LAST_NAME)                              AS APRV_LN
                FROM HRIS_ATTENDANCE_REQUEST AR
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=AR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=AR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=AR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON AR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE
                E.STATUS='E'" . $retiredFlag . "
                AND E.RETIRED_FLAG='N'
                AND (E1.STATUS    =
                  CASE
                    WHEN E1.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E1.STATUS  IS NULL)
                AND (E2.STATUS =
                  CASE
                    WHEN E2.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E2.STATUS    IS NULL)
                AND (RECM.STATUS =
                  CASE
                    WHEN RECM.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR RECM.STATUS  IS NULL)
                AND (APRV.STATUS =
                  CASE
                    WHEN APRV.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR APRV.STATUS IS NULL)";

        if ($recomApproveId != null) {
            if ($attendanceRequestStatusId == -1) {
                $sql .= " AND ((RA.RECOMMEND_BY=" . $recomApproveId . " AND  AR.STATUS='RQ')"
                        . "OR (AR.RECOMMENDED_BY=" . $recomApproveId . " AND (AR.STATUS='RC' OR AR.STATUS='R' OR AR.STATUS='AP')) "
                        . "OR (RA.APPROVED_BY=" . $recomApproveId . " AND  AR.STATUS='RC' ) "
                        . "OR (AR.APPROVED_BY=" . $recomApproveId . " AND (AR.STATUS='AP' OR (AR.STATUS='R' AND AR.APPROVED_DT IS NOT NULL))) )";
            } else if ($attendanceRequestStatusId == 'RQ') {
                $sql .= " AND (RA.RECOMMEND_BY=" . $recomApproveId . " AND AR.STATUS='RQ')";
            } else if ($attendanceRequestStatusId == 'RC') {
                $sql .= " AND AR.STATUS='RC' AND
                    (AR.RECOMMENDED_BY=" . $recomApproveId . " OR RA.APPROVED_BY=" . $recomApproveId . ")";
            } else if ($attendanceRequestStatusId == 'AP') {
                $sql .= " AND AR.STATUS='AP' AND
                    (LA.RECOMMENDED_BY=" . $recomApproveId . " OR LA.APPROVED_BY=" . $recomApproveId . ")";
            } else if ($attendanceRequestStatusId == 'R') {
                $sql .= " AND AR.STATUS='" . $attendanceRequestStatusId . "' AND
                    ((AR.RECOMMENDED_BY=" . $recomApproveId . ") OR (AR.APPROVED_BY=" . $recomApproveId . " AND AR.APPROVED_DT IS NOT NULL) )";
            }
        } else {
            if ($attendanceRequestStatusId != -1) {
                $sql .= "AND AR.STATUS = '{$attendanceRequestStatusId}'";
            }
        }

        if ($fromDate != null) {
            $sql .= " AND AR.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND AR.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
        }

        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
        }

        if ($companyId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::COMPANY_ID . "= $companyId)";
        }

        if ($branchId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)";
        }
        if ($departmentId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DEPARTMENT_ID . "= $departmentId)";
        }
        if ($designationId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DESIGNATION_ID . "= $designationId)";
        }
        if ($positionId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::POSITION_ID . "= $positionId)";
        }
        if ($serviceTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_TYPE_ID . "= $serviceTypeId)";
        }
        if ($serviceEventTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_EVENT_TYPE_ID . "= $serviceEventTypeId)";
        }

        $statement = $this->adapter->query($sql);
//        print_r($statement->getSql());
//        die();
        $result = $statement->execute();
        return $result;
    }

}
