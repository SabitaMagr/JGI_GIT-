<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveApproveRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    private $tableGatewayLeaveAssign;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveApply::TABLE_NAME, $adapter);
        $this->tableGatewayLeaveAssign = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        // TODO: Implement add() method.
    }

    public function getAllRequest($id) {
        $sql = "
                SELECT 
                  LA.ID                  AS ID,
                  LA.EMPLOYEE_ID,
                  INITCAP(E.FULL_NAME)   AS FULL_NAME,
                  INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                  INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_AD,
                  BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_BS,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_AD,
                  BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_BS,
                  LA.NO_OF_DAYS,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_AD,
                  BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_BS,
                  LA.HALF_DAY AS HALF_DAY,
                  (CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') THEN 'Full Day' WHEN (LA.HALF_DAY = 'F') THEN 'First Half' ELSE 'Second Half' END) AS HALF_DAY_DETAIL,
                  LA.GRACE_PERIOD AS GRACE_PERIOD,
                  (CASE WHEN LA.GRACE_PERIOD = 'E' THEN 'Early' WHEN LA.GRACE_PERIOD = 'L' THEN 'Late' ELSE '-' END) AS GRACE_PERIOD_DETAIL,
                   LA.REMARKS AS REMARKS,                  
                  LA.STATUS                            AS STATUS,
                  LEAVE_STATUS_DESC(LA.STATUS) AS STATUS_DETAIL,
                  LA.RECOMMENDED_BY AS RECOMMENDED_BY,
                  INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT,
                  LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                  LA.APPROVED_BY AS APPROVED_BY,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT,
                  LA.APPROVED_REMARKS AS APPROVED_REMARKS,
                  RA.RECOMMEND_BY                                         AS RECOMMENDER,
                  RA.APPROVED_BY                                          AS APPROVER,
                  LS.APPROVED_FLAG                                        AS APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))       AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                          AS SUB_EMPLOYEE_ID,
                  REC_APP_ROLE(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                LEFT JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LA.ID              = LS.LEAVE_REQUEST_ID
                LEFT JOIN HRIS_EMPLOYEES U
                ON(U.EMPLOYEE_ID   = RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID   =RA.APPROVED_BY)
                WHERE E.STATUS        ='E'
                AND E.RETIRED_FLAG    ='N'
                AND ((RA.RECOMMEND_BY= U.EMPLOYEE_ID AND LA.STATUS='RQ') OR (RA.APPROVED_BY= U.EMPLOYEE_ID AND LA.STATUS='RC') )
                AND U.EMPLOYEE_ID={$id}
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL)
                ORDER BY LA.REQUESTED_DT DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [LeaveApply::ID => $id]);
        EntityHelper::rawQueryResult($this->adapter, "
                   DECLARE
                      V_ID HRIS_EMPLOYEE_LEAVE_REQUEST.ID%TYPE;
                      V_STATUS HRIS_EMPLOYEE_LEAVE_REQUEST.STATUS%TYPE;
                      V_START_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.START_DATE%TYPE;
                      V_EMPLOYEE_ID HRIS_EMPLOYEE_LEAVE_REQUEST.EMPLOYEE_ID%TYPE;
                    BEGIN
                      SELECT ID,
                        STATUS,
                        START_DATE,
                        EMPLOYEE_ID
                      INTO V_ID,
                        V_STATUS,
                        V_START_DATE,
                        V_EMPLOYEE_ID
                      FROM HRIS_EMPLOYEE_LEAVE_REQUEST
                      WHERE ID                                    = {$id};
                      IF(V_STATUS IN ('AP','C') AND V_START_DATE <=TRUNC(SYSDATE)) THEN
                        HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID);
                      END IF;
                    END;
    ");
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        $sql = "SELECT INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))    AS REQUESTED_DT,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))     AS APPROVED_DT,
                  LA.STATUS                                           AS STATUS,
                  LA.ID                                               AS ID,
                  LA.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
                  LA.NO_OF_DAYS                                       AS NO_OF_DAYS,
                  LA.HALF_DAY                                         AS HALF_DAY,
                  LA.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                  LA.LEAVE_ID                                         AS LEAVE_ID,
                  LA.REMARKS                                          AS REMARKS,
                  LA.RECOMMENDED_BY                                   AS RECOMMENDED_BY,
                  LA.APPROVED_BY                                      AS APPROVED_BY,
                  LA.RECOMMENDED_REMARKS                              AS RECOMMENDED_REMARKS,
                  LA.APPROVED_REMARKS                                 AS APPROVED_REMARKS,
                  LA.GRACE_PERIOD                                     AS GRACE_PERIOD,
                  L.PAID                                              AS PAID,
                  L.ALLOW_HALFDAY                                     AS ALLOW_HALFDAY,
                  LS.EMPLOYEE_ID                                      AS SUB_EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))   AS SUB_APPROVED_DATE,
                  LS.REMARKS                                          AS SUB_REMARKS,
                  LS.APPROVED_FLAG                                    AS SUB_APPROVED_FLAG,
                  INITCAP(E.FULL_NAME)                                AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                               AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                               AS APPROVED_BY_NAME,
                  RA.RECOMMEND_BY                                     AS RECOMMENDER_ID,
                  RA.APPROVED_BY                                      AS APPROVER_ID,
                  INITCAP(RECM.FULL_NAME)                             AS RECOMMENDER_NAME,
                  INITCAP(APRV.FULL_NAME)                             AS APPROVER_NAME,
                  ELA.TOTAL_DAYS                                      AS TOTAL_DAYS,
                  ELA.BALANCE                                         AS BALANCE
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LS.LEAVE_REQUEST_ID=LA.ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON RA.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID=RA.RECOMMEND_BY
                LEFT JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID=RA.APPROVED_BY,
                  HRIS_MONTH_CODE MTH,
                  HRIS_EMPLOYEE_LEAVE_ASSIGN ELA
                WHERE LA.ID = {$id}
                AND TRUNC(LA.START_DATE) BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                AND LA.EMPLOYEE_ID            =ELA.EMPLOYEE_ID
                AND LA.LEAVE_ID               =ELA.LEAVE_ID
                AND (ELA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN L.IS_MONTHLY ='Y'
                    THEN MTH.FISCAL_YEAR_MONTH_NO
                  END
                OR ELA.FISCAL_YEAR_MONTH_NO IS NULL)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function assignedLeaveDetail($leaveId, $employeeId) {
        $result = $this->tableGatewayLeaveAssign->select(['EMPLOYEE_ID' => $employeeId, 'LEAVE_ID' => $leaveId]);
        return $result->current();
    }

    public function updateLeaveBalance($leaveId, $employeeId, $balance) {
        $this->tableGatewayLeaveAssign->update(["BALANCE" => $balance], ['LEAVE_ID' => $leaveId, 'EMPLOYEE_ID' => $employeeId]);
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

}
