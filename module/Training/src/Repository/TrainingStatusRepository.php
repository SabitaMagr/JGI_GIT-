<?php

namespace Training\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;

class TrainingStatusRepository implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
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
        $requestStatusId = $data['requestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];



        $sql = "SELECT TR.REQUEST_ID,
                  TR.EMPLOYEE_ID,
                  INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD,
                  BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS,
                  TR.APPROVED_BY,
                  TR.RECOMMENDED_BY,
                  TR.REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.DURATION
                    ELSE T.DURATION
                  END) AS DURATION ,
                  TR.DESCRIPTION,
                  INITCAP(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TITLE
                    ELSE T.TRAINING_NAME
                  END) AS TITLE,
                  TR.STATUS,
                  TR.TRAINING_ID,
                  TRAINING_TYPE_DESC(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TRAINING_TYPE
                    ELSE T.TRAINING_TYPE
                  END) AS TRAINING_TYPE,
                  TR.RECOMMENDED_REMARKS,
                  TR.APPROVED_REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))
                  END) AS START_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.START_DATE)
                    ELSE BS_DATE(T.START_DATE)
                  END) AS START_DATE_BS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))
                  END) AS END_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.END_DATE)
                    ELSE BS_DATE(T.END_DATE)
                  END)                                                            AS END_DATE_BS,
                  INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY'))            AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY'))               AS APPROVED_DATE,
                  INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY'))               AS MODIFIED_DATE,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(T.TRAINING_NAME)                                        AS TRAINING_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER,
                  RA.APPROVED_BY                                                  AS APPROVER,
                  LEAVE_STATUS_DESC(TR.STATUS)                                    AS STATUS ,
                  REC_APP_ROLE(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                FROM HRIS_EMPLOYEE_TRAINING_REQUEST TR
                LEFT OUTER JOIN HRIS_TRAINING_MASTER_SETUP T
                ON T.TRAINING_ID=TR.TRAINING_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=TR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=TR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON TR.EMPLOYEE_ID = RA.EMPLOYEE_ID
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
                OR APRV.STATUS   IS NULL)
                AND U.EMPLOYEE_ID = {$recomApproveId}";
        if ($requestStatusId != -1) {
            $sql .= " AND TR.STATUS ='" . $requestStatusId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND ((TR.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')) OR (T.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')))";
        }

        if ($toDate != null) {
            $sql .= "AND ((TR.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')) OR (T.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')))";
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

        $sql .= " ORDER BY TR.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getTrainingRequestList($data) {
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



        $sql = "SELECT TR.REQUEST_ID,
                  TR.EMPLOYEE_ID,
                  INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD,
                  BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS,
                  TR.APPROVED_BY,
                  TR.RECOMMENDED_BY,
                  TR.REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.DURATION
                    ELSE T.DURATION
                  END) AS DURATION ,
                  TR.DESCRIPTION,
                  INITCAP(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TITLE
                    ELSE T.TRAINING_NAME
                  END) AS TITLE,
                  TR.STATUS,
                  TR.TRAINING_ID,
                  TRAINING_TYPE_DESC(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TRAINING_TYPE
                    ELSE T.TRAINING_TYPE
                  END) AS TRAINING_TYPE,
                  TR.RECOMMENDED_REMARKS,
                  TR.APPROVED_REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))
                  END) AS START_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.START_DATE)
                    ELSE BS_DATE(T.START_DATE)
                  END) AS START_DATE_BS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))
                  END) AS END_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.END_DATE)
                    ELSE BS_DATE(T.END_DATE)
                  END)                                                            AS END_DATE_BS,
                  INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY'))            AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY'))               AS APPROVED_DATE,
                  INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY'))               AS MODIFIED_DATE,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(T.TRAINING_NAME)                                        AS TRAINING_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER,
                  RA.APPROVED_BY                                                  AS APPROVER,
                  LEAVE_STATUS_DESC(TR.STATUS)                                    AS STATUS 
                FROM HRIS_EMPLOYEE_TRAINING_REQUEST TR
                LEFT OUTER JOIN HRIS_TRAINING_MASTER_SETUP T
                ON T.TRAINING_ID=TR.TRAINING_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=TR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=TR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON TR.EMPLOYEE_ID = RA.EMPLOYEE_ID
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
                OR APRV.STATUS   IS NULL)";
        if ($requestStatusId != -1) {
            $sql .= " AND TR.STATUS ='" . $requestStatusId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND ((TR.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')) OR (T.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')))";
        }

        if ($toDate != null) {
            $sql .= "AND ((TR.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')) OR (T.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')))";
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

        $sql .= " ORDER BY TR.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
