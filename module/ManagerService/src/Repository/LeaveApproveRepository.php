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
                SELECT INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                  LA.NO_OF_DAYS,
                  INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))   AS START_DATE,
                  BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_N,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))     AS END_DATE,
                  BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_N,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE,
                  BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_N,
                  LA.EMPLOYEE_ID,
                  LA.ID                  AS ID,
                  INITCAP(E.FIRST_NAME)  AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)   AS LAST_NAME,
                  INITCAP(E.FULL_NAME)   AS FULL_NAME,
                  LA.RECOMMENDED_BY,
                  LA.APPROVED_BY,
                  RA.RECOMMEND_BY                                         AS RECOMMENDER,
                  RA.APPROVED_BY                                          AS APPROVER,
                  LS.APPROVED_FLAG                                        AS APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))       AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                          AS SUB_EMPLOYEE_ID,
                  LEAVE_STATUS_DESC(LA.STATUS)                            AS STATUS,
                  REC_APP_ROLE({$id},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME({$id},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
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
                WHERE E.STATUS        ='E'
                AND E.RETIRED_FLAG    ='N'
                AND ((RA.RECOMMEND_BY = {$id}
                AND LA.STATUS         ='RQ'
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL)))
                OR (RA.APPROVED_BY = {$id}
                AND LA.STATUS      ='RC')  ORDER BY LA.REQUESTED_DT DESC";
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
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT"),
            new Expression("INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LA.ID AS ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.HALF_DAY AS HALF_DAY"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LA.APPROVED_BY AS APPROVED_BY"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LA.GRACE_PERIOD AS GRACE_PERIOD"),
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
                ->join(['LS' => "HRIS_LEAVE_SUBSTITUTE"], "LS.LEAVE_REQUEST_ID=LA.ID", ['SUB_EMPLOYEE_ID' => 'EMPLOYEE_ID', 'SUB_APPROVED_DATE' => new Expression("INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))"), 'SUB_REMARKS' => "REMARKS", 'SUB_APPROVED_FLAG' => "APPROVED_FLAG"], "left")
                ->join(['L' => "HRIS_LEAVE_MASTER_SETUP"], "L.LEAVE_ID=LA.LEAVE_ID", ['PAID'], "left");

        $select->where([
            "LA.ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
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
