<?php

namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class LeaveStatusRepository implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        // TODO: Implement add() method.
    }

    public function edit(Model $model, $id) {
        // TODO: Implement edit() method.
    }

    public function getAllRequest($status = null, $date = null, $branchId = NULL, $employeeId = NULL) {
        $sql = "SELECT INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                LA.NO_OF_DAYS,
                INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))     AS START_DATE,
                INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))       AS END_DATE,
                INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))   AS APPLIED_DATE,
                LA.STATUS                                 AS STATUS,
                LA.ID                                     AS ID,
                INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT,
                INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))    AS APPROVED_DT,
                INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                INITCAP(E.MIDDLE_NAME AS MIDDLE_NAME,
                INITCAP(E.LAST_NAME) AS LAST_NAME
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA,
                  HRIS_LEAVE_MASTER_SETUP L,
                  HRIS_EMPLOYEES E
                WHERE L.STATUS   ='E'
                AND E.STATUS     ='E'
                AND L.LEAVE_ID   =LA.LEAVE_ID
                AND E.EMPLOYEE_ID=LA.EMPLOYEE_ID ";
        if ($status != null) {
            $sql .= " AND LA.STATUS ='" . $status . "'";
        }
        if ($date != null) {
            $sql .= "AND (" . $date->getExpression() . " between LA.START_DATE AND LA.END_DATE)";
        }

        if ($branchId != null) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)";
        }

        if ($employeeId != null) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
        }
        $statement = $this->adapter->query($sql);

        $result = $statement->execute();
        return $result;
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LeaveApply::class, NULL, [
                    LeaveApply::START_DATE,
                    LeaveApply::REQUESTED_DT,
                    LeaveApply::END_DATE,
                    LeaveApply::APPROVED_DT
                        ], NULL, NULL, NULL, 'LA'), false);


        $select->from(['LA' => LeaveApply::TABLE_NAME])
//                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], "left")
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)')], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left");

        $select->where([
            "LA.ID=" . $id
        ]);

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
        $leaveId = $data['leaveId'];
        $leaveRequestStatusId = $data['leaveRequestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];


        $sql = "SELECT INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                  L.LEAVE_CODE,
                  LA.NO_OF_DAYS,
                  INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))                  AS START_DATE_AD,
                  BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))                  AS START_DATE_BS,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))                    AS END_DATE_AD,
                  BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))                    AS END_DATE_BS,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))                AS APPLIED_DATE_AD,
                  BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))                AS APPLIED_DATE_BS,
                  LEAVE_STATUS_DESC(LA.STATUS)                                    AS STATUS,
                  REC_APP_ROLE(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE,
                  LA.ID                                                           AS ID,
                  LA.EMPLOYEE_ID                                                  AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY'))              AS RECOMMENDED_DT,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))                 AS APPROVED_DT,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                                           AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                                           AS APPROVED_BY_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER_ID,
                  RA.APPROVED_BY                                                  AS APPROVER_ID,
                  INITCAP(RECM.FULL_NAME)                                         AS RECOMMENDER_NAME,
                  INITCAP(APRV.FULL_NAME)                                         AS APPROVER_NAME,
                  LA.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
                  LA.APPROVED_BY                                                  AS APPROVED_BY,
                  LA.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
                  LA.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
                  LS.APPROVED_FLAG                                                AS SUB_APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))               AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                                  AS SUB_EMPLOYEE_ID
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT OUTER JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON LA.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                LEFT OUTER JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LA.ID = LS.LEAVE_REQUEST_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES U
                ON (U.EMPLOYEE_ID=RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID =RA.APPROVED_BY)
                WHERE L.STATUS   ='E'
                AND E.STATUS     ='E'
                AND (E1.STATUS   =
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
                OR APRV.STATUS       IS NULL)
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL)
                AND U.EMPLOYEE_ID  ={$recomApproveId}";
        if ($leaveRequestStatusId != -1) {
            $sql .= " AND  LA.STATUS='{$leaveRequestStatusId}')";
        }

        if ($leaveId != -1) {
            $sql .= " AND LA.LEAVE_ID ='" . $leaveId . "'";
        }


        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
        }

        if ($fromDate != null) {
            $sql .= " AND LA.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND LA.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
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


        $sql .= " ORDER BY LA.REQUESTED_DT DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getLeaveRequestList($data) {
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
        $leaveId = $data['leaveId'];
        $leaveRequestStatusId = $data['leaveRequestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];


        $sql = "SELECT INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                  L.LEAVE_CODE,
                  LA.NO_OF_DAYS,
                  INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))                  AS START_DATE_AD,
                  BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))                  AS START_DATE_BS,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))                    AS END_DATE_AD,
                  BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))                    AS END_DATE_BS,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))                AS APPLIED_DATE_AD,
                  BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))                AS APPLIED_DATE_BS,
                  LEAVE_STATUS_DESC(LA.STATUS)                                    AS STATUS,
                  LA.ID                                                           AS ID,
                  LA.EMPLOYEE_ID                                                  AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY'))              AS RECOMMENDED_DT,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))                 AS APPROVED_DT,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                                           AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                                           AS APPROVED_BY_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER_ID,
                  RA.APPROVED_BY                                                  AS APPROVER_ID,
                  INITCAP(RECM.FULL_NAME)                                         AS RECOMMENDER_NAME,
                  INITCAP(APRV.FULL_NAME)                                         AS APPROVER_NAME,
                  LA.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
                  LA.APPROVED_BY                                                  AS APPROVED_BY,
                  LA.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
                  LA.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
                  LS.APPROVED_FLAG                                                AS SUB_APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))               AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                                  AS SUB_EMPLOYEE_ID
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT OUTER JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON LA.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                LEFT OUTER JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LA.ID = LS.LEAVE_REQUEST_ID
                WHERE L.STATUS   ='E'
                AND E.STATUS     ='E'
                AND (E1.STATUS   =
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
                OR APRV.STATUS       IS NULL)
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL)";
        if ($leaveRequestStatusId != -1) {
            $sql .= " AND  LA.STATUS='{$leaveRequestStatusId}'";
        }

        if ($leaveId != null && $leaveId != -1) {
            $sql .= " AND LA.LEAVE_ID ='" . $leaveId . "'";
        }


        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
        }

        if ($fromDate != null) {
            $sql .= " AND LA.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND LA.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
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

        $sql .= " ORDER BY LA.REQUESTED_DT DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
