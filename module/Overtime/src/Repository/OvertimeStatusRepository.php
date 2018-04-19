<?php

namespace Overtime\Repository;

use Application\Model\Model;
use SelfService\Model\Overtime;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class OvertimeStatusRepository {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Overtime::TABLE_NAME, $adapter);
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [Overtime::OVERTIME_ID => $id]);
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("OT.OVERTIME_ID AS OVERTIME_ID"),
            new Expression("OT.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE"),
            new Expression("INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS"),
            new Expression("OT.DESCRIPTION AS IN_DESCRIPTION"),
            new Expression("OT.REMARKS AS REMARKS"),
            new Expression("OT.TOTAL_HOUR AS TOTAL_HOUR"),
            new Expression("MIN_TO_HOUR(OT.TOTAL_HOUR) AS TOTAL_HOUR_DETAIL"),
            new Expression("OT.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(OT.STATUS) AS STATUS_DETAIL"),
            new Expression("OT.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("OT.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("OT.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("OT.APPROVED_REMARKS AS APPROVED_REMARKS")
        ]);
        $select->from(['OT' => Overtime::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=OT.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=OT.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=OT.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=OT.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where(["OT.OVERTIME_ID" => $id]);
        $select->order("OT.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllRequest($id) {
        $sql = "SELECT 
                    OT.OVERTIME_ID,
                    OT.EMPLOYEE_ID,
                    INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE,
                    BS_DATE(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_N,
                    INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    BS_DATE(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_N,
                    OT.APPROVED_BY,
                    OT.RECOMMENDED_BY,
                    OT.REMARKS,
                    OT.DESCRIPTION,
                    OT.RECOMMENDED_REMARKS,
                    OT.APPROVED_REMARKS,
                    TRUNC(OT.TOTAL_HOUR/60,0)
                  ||':'
                  ||MOD(OT.TOTAL_HOUR,60) AS TOTAL_HOUR,
                    INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    INITCAP(TO_CHAR(OT.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    INITCAP(E.FULL_NAME) AS FULL_NAME,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER,
                    LEAVE_STATUS_DESC(OT.STATUS)                     AS STATUS,
                    REC_APP_ROLE({$id},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                    REC_APP_ROLE_NAME({$id},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                    FROM HRIS_OVERTIME OT
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=OT.EMPLOYEE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE  E.STATUS='E'
                    AND E.RETIRED_FLAG='N' 
                    AND ((RA.RECOMMEND_BY= {$id} AND OT.STATUS='RQ') OR (RA.APPROVED_BY= {$id} AND OT.STATUS='RC') )
                    ORDER BY OT.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getFilteredRecord($data, $recomApproveId) {
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
        $requestStatusId = $data['requestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];


        $sql = "SELECT INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY'))          AS OVERTIME_DATE_AD,
                  BS_DATE(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY'))               AS OVERTIME_DATE_BS,
                  INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY'))              AS REQUESTED_DATE_AD,
                  BS_DATE(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY'))              AS REQUESTED_DATE_BS,
                  LEAVE_STATUS_DESC(OT.STATUS)                                    AS STATUS,
                  REC_APP_ROLE(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE,
                  OT.REMARKS                                                      AS REMARKS,
                  OT.DESCRIPTION                                                  AS DESCRIPTION,
                  OT.EMPLOYEE_ID                                                  AS EMPLOYEE_ID,
                  OT.OVERTIME_ID                                                  AS OVERTIME_ID,
                  OT.MODIFIED_DATE                                                AS MODIFIED_DATE,
                  INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY'))            AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY'))               AS APPROVED_DATE,
                  OT.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
                  OT.APPROVED_BY                                                  AS APPROVED_BY,
                  OT.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
                  OT.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
                  TRUNC(OT.TOTAL_HOUR/60,2)                                       AS TOTAL_HOUR,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                                           AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                                           AS APPROVED_BY_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER_ID,
                  RA.APPROVED_BY                                                  AS APPROVER_ID,
                  INITCAP(RECM.FIRST_NAME)                                        AS RECOMMENDER_NAME,
                  INITCAP(APRV.FIRST_NAME)                                        AS APPROVER_NAME
                FROM HRIS_OVERTIME OT
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=OT.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=OT.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=OT.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON OT.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES U
                ON (U.EMPLOYEE_ID=RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID =RA.APPROVED_BY)
                WHERE E.STATUS   ='E'
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
                OR APRV.STATUS IS NULL)
                AND U.EMPLOYEE_ID = {$recomApproveId}";

        if ($requestStatusId != -1) {
            $sql .= " AND OT.STATUS ='" . $requestStatusId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND OT.OVERTIME_DATE>=TO_DATE('" . $fromDate . "','DD-MON-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND OT.OVERTIME_DATE<=TO_DATE('" . $toDate . "','DD-MON-YYYY')";
        }

        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
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

        $sql .= " ORDER BY OT.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getOTRequestList($data) {
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
        $requestStatusId = $data['requestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];


        $sql = "SELECT INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY'))          AS OVERTIME_DATE_AD,
                  BS_DATE(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY'))               AS OVERTIME_DATE_BS,
                  INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY'))              AS REQUESTED_DATE_AD,
                  BS_DATE(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY'))              AS REQUESTED_DATE_BS,
                  LEAVE_STATUS_DESC(OT.STATUS)                                    AS STATUS,
                  OT.REMARKS                                                      AS REMARKS,
                  OT.DESCRIPTION                                                  AS DESCRIPTION,
                  OT.EMPLOYEE_ID                                                  AS EMPLOYEE_ID,
                  OT.OVERTIME_ID                                                  AS OVERTIME_ID,
                  OT.MODIFIED_DATE                                                AS MODIFIED_DATE,
                  INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY'))            AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY'))               AS APPROVED_DATE,
                  OT.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
                  OT.APPROVED_BY                                                  AS APPROVED_BY,
                  OT.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
                  OT.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
                  TRUNC(OT.TOTAL_HOUR/60,2)                                       AS TOTAL_HOUR,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                                           AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                                           AS APPROVED_BY_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER_ID,
                  RA.APPROVED_BY                                                  AS APPROVER_ID,
                  INITCAP(RECM.FIRST_NAME)                                        AS RECOMMENDER_NAME,
                  INITCAP(APRV.FIRST_NAME)                                        AS APPROVER_NAME
                FROM HRIS_OVERTIME OT
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=OT.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=OT.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=OT.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON OT.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE E.STATUS   ='E'
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
                OR APRV.STATUS IS NULL)";

        if ($requestStatusId != -1) {
            $sql .= " AND OT.STATUS ='" . $requestStatusId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND OT.OVERTIME_DATE>=TO_DATE('" . $fromDate . "','DD-MON-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND OT.OVERTIME_DATE<=TO_DATE('" . $toDate . "','DD-MON-YYYY')";
        }

        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
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

        $sql .= " ORDER BY OT.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
