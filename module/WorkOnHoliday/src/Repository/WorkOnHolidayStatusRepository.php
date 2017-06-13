<?php

namespace WorkOnHoliday\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;

class WorkOnHolidayStatusRepository implements RepositoryInterface {

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
        $holidayId = $data['holidayId'];
        $requestStatusId = $data['requestStatusId'];

        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        } else {
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }

        $sql = "SELECT INITCAP(H.HOLIDAY_ENAME) AS HOLIDAY_ENAME,WH.DURATION,
                INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE,
                INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE,
                INITCAP(TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                WH.STATUS AS STATUS,
                WH.EMPLOYEE_ID AS EMPLOYEE_ID,
                WH.ID AS ID,
                WH.REMARKS AS REMARKS,
                INITCAP(TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                INITCAP(TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                INITCAP(E.FIRST_NAME) AS FIRST_NAME,INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,INITCAP(E.LAST_NAME) AS LAST_NAME,
                INITCAP(E1.FIRST_NAME) AS FN1,INITCAP(E1.MIDDLE_NAME) AS MN1,INITCAP(E1.LAST_NAME) AS LN1,
                INITCAP(E2.FIRST_NAME) AS FN2,INITCAP(E2.MIDDLE_NAME) AS MN2,INITCAP(E2.LAST_NAME) AS LN2,
                RA.RECOMMEND_BY AS RECOMMENDER,
                RA.APPROVED_BY AS APPROVER,
                INITCAP(RECM.FIRST_NAME) AS RECM_FN,INITCAP(RECM.MIDDLE_NAME) AS RECM_MN,INITCAP(RECM.LAST_NAME) AS RECM_LN,
                INITCAP(APRV.FIRST_NAME) AS APRV_FN,INITCAP(APRV.MIDDLE_NAME) AS APRV_MN,INITCAP(APRV.LAST_NAME) AS APRV_LN,
                WH.RECOMMENDED_BY AS RECOMMENDED_BY,
                WH.APPROVED_BY AS APPROVED_BY,
                WH.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                WH.APPROVED_REMARKS AS APPROVED_REMARKS
                FROM HRIS_EMPLOYEE_WORK_HOLIDAY WH
                LEFT OUTER JOIN HRIS_HOLIDAY_MASTER_SETUP H ON
                H.HOLIDAY_ID=WH.HOLIDAY_ID 
                LEFT OUTER JOIN HRIS_EMPLOYEES E ON
                E.EMPLOYEE_ID=WH.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1 ON
                E1.EMPLOYEE_ID=WH.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2 ON
                E2.EMPLOYEE_ID=WH.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA ON
                WH.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM ON
                RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV ON
                APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE 
                H.STATUS='E' AND
                E.STATUS='E'" . $retiredFlag . "              
                AND
                (E1.STATUS = CASE WHEN E1.STATUS IS NOT NULL
                         THEN ('E')     
                    END OR  E1.STATUS is null) AND
                (E2.STATUS = CASE WHEN E2.STATUS IS NOT NULL
                         THEN ('E')       
                    END OR  E2.STATUS is null) AND
                (RECM.STATUS = CASE WHEN RECM.STATUS IS NOT NULL
                         THEN ('E')       
                    END OR  RECM.STATUS is null) AND
                (APRV.STATUS = CASE WHEN APRV.STATUS IS NOT NULL
                         THEN ('E')       
                    END OR  APRV.STATUS is null)";
        if ($recomApproveId == null) {
            if ($requestStatusId != -1) {
                $sql .= " AND WH.STATUS ='" . $requestStatusId . "'";
            }
        }
        if ($recomApproveId != null) {
            if ($requestStatusId == -1) {
                $sql .= " AND ((RA.RECOMMEND_BY=" . $recomApproveId . " AND  WH.STATUS='RQ') "
                        . "OR (WH.RECOMMENDED_BY=" . $recomApproveId . " AND (WH.STATUS='RC' OR WH.STATUS='R' OR WH.STATUS='AP')) "
                        . "OR (RA.APPROVED_BY=" . $recomApproveId . " AND  WH.STATUS='RC' ) "
                        . "OR (WH.APPROVED_BY=" . $recomApproveId . " AND (WH.STATUS='AP' OR (WH.STATUS='R' AND WH.APPROVED_DATE IS NOT NULL))) )";
            } else if ($requestStatusId == 'RQ') {
                $sql .= " AND (RA.RECOMMEND_BY=" . $recomApproveId . " AND WH.STATUS='RQ')";
            } else if ($requestStatusId == 'RC') {
                $sql .= " AND WH.STATUS='RC' AND
                    (WH.RECOMMENDED_BY=" . $recomApproveId . " OR RA.APPROVED_BY=" . $recomApproveId . ")";
            } else if ($requestStatusId == 'AP') {
                $sql .= " AND WH.STATUS='AP' AND
                    (WH.RECOMMENDED_BY=" . $recomApproveId . " OR WH.APPROVED_BY=" . $recomApproveId . ")";
            } else if ($requestStatusId == 'R') {
                $sql .= " AND WH.STATUS='" . $requestStatusId . "' AND
                    ((WH.RECOMMENDED_BY=" . $recomApproveId . ") OR (WH.APPROVED_BY=" . $recomApproveId . " AND WH.APPROVED_DATE IS NOT NULL) )";
            }
        }

        if ($holidayId != -1) {
            $sql .= " AND WH.HOLIDAY_ID ='" . $holidayId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND WH.FROM_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= " AND WH.TO_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
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

        $sql .= " ORDER BY WH.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getAttendedHolidayList($employeeId) {
        return EntityHelper::rawQueryResult($this->adapter, "
                    SELECT H.HOLIDAY_ID,
                      H.HOLIDAY_CODE,
                      H.HOLIDAY_ENAME,
                      H.HOLIDAY_LNAME,
                      TO_CHAR(H.START_DATE,'DD-MON-YYYY') AS START_DATE,
                      TO_CHAR(H.END_DATE,'DD-MON-YYYY')   AS END_DATE,
                      H.HALFDAY,
                      H.FISCAL_YEAR
                    FROM HRIS_HOLIDAY_MASTER_SETUP H
                    JOIN HRIS_EMPLOYEE_HOLIDAY EH
                    ON (H.HOLIDAY_ID=EH.HOLIDAY_ID),
                      (SELECT MIN(ATTENDANCE_DT) AS MIN_ATTENDANCE_DT,
                        MAX(ATTENDANCE_DT)       AS MAX_ATTENDANCE_DT
                      FROM HRIS_ATTENDANCE_DETAIL
                      WHERE EMPLOYEE_ID={$employeeId}
                      )A
                    WHERE H.STATUS     ='E'
                    AND EH.EMPLOYEE_ID = {$employeeId}
                    AND H.START_DATE BETWEEN A.MIN_ATTENDANCE_DT AND A.MAX_ATTENDANCE_DT");
    }

}
