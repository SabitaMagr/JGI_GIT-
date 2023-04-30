<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
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
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $sql = "
                SELECT 
                  LA.ID                  AS ID,
                  LA.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
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
                  REC_APP_ROLE(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  )      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  ) AS YOUR_ROLE,
                  CASE WHEN ( ALR.R_A_ID IS NOT NULL OR ALA.R_A_ID  IS NOT NULL ) THEN 'SECONDARY' ELSE 'PRIMARY' END AS PRI_SEC
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
                LEFT JOIN HRIS_ALTERNATE_R_A ALR
                ON(ALR.R_A_FLAG='R' AND ALR.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALR.R_A_ID=:id)
                LEFT JOIN HRIS_ALTERNATE_R_A ALA
                ON(ALA.R_A_FLAG='A' AND ALA.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALA.R_A_ID=:id)
                -- CHANGES
                LEFT JOIN hris_rec_app_override RAO ON E.EMPLOYEE_ID=RAO.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES U
                ON(
                (
                (U.EMPLOYEE_ID   = RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID   =RA.APPROVED_BY
                OR U.EMPLOYEE_ID   =ALR.R_A_ID
                OR U.EMPLOYEE_ID   =ALA.R_A_ID)
               AND L.ENABLE_OVERRIDE='N' )
               OR
               (
                (U.EMPLOYEE_ID   = RAO.recommender
                OR U.EMPLOYEE_ID   =RAO.approver
               ) AND L.ENABLE_OVERRIDE='Y' 
               )
               
                )
                
             -- CHANGES
                
                
                WHERE E.STATUS        ='E'
                AND E.RETIRED_FLAG    ='N'
                AND ((
                (
                (
                (RA.RECOMMEND_BY= U.EMPLOYEE_ID)
                OR(ALR.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                ) OR (RAO.recommender=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='Y' )
                )
                AND LA.STATUS IN ('RQ')) 
                OR (
                (
                (
                (RA.APPROVED_BY= U.EMPLOYEE_ID)
                OR(ALA.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                )
                OR ( RAO.APPROVER=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='N' )
                )
                AND LA.STATUS IN ('RC')) )
                AND U.EMPLOYEE_ID=:id
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL
                OR LA.STATUS IN ('CP','CR'))
                ORDER BY LA.REQUESTED_DT DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [LeaveApply::ID => $id]);
        
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        
        EntityHelper::rawQueryResult($this->adapter, "
                   DECLARE
                      V_ID HRIS_EMPLOYEE_LEAVE_REQUEST.ID%TYPE;
                      V_STATUS HRIS_EMPLOYEE_LEAVE_REQUEST.STATUS%TYPE;
                      V_START_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.START_DATE%TYPE;
                      V_END_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.END_DATE%TYPE;
                      V_EMPLOYEE_ID HRIS_EMPLOYEE_LEAVE_REQUEST.EMPLOYEE_ID%TYPE;
                    BEGIN
                      SELECT ID,
                        STATUS,
                        START_DATE,
                        END_DATE,
                        EMPLOYEE_ID
                      INTO V_ID,
                        V_STATUS,
                        V_START_DATE,
                        V_END_DATE,
                        V_EMPLOYEE_ID
                      FROM HRIS_EMPLOYEE_LEAVE_REQUEST
                      WHERE ID                                    = :id;
                      IF(V_STATUS IN ('AP','C','R')) THEN
                        HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID,V_END_DATE);
                      END IF;
                    END;
    ",$boundedParameter);
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
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
                  ELA.BALANCE                                         AS BALANCE,
                  LA.HARDCOPY_SIGNED_FLAG                             AS HR_APPROVED,
                  (CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N')
                  then LA.NO_OF_DAYS
                  else LA.NO_OF_DAYS/2
                  END) AS ACTUAL_DAYS,
                  (CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') 
                  THEN 'Full Day' 
                  WHEN (LA.HALF_DAY = 'F') THEN 'First Half' 
                  ELSE 'Second Half' END) AS HALF_DAY_DETAIL
                ,CASE WHEN SUB_REF_ID IS NOT NULL THEN 
                INITCAP(L.LEAVE_ENAME)||'('||SLR.SUB_NAME||')' END AS LEAVE_ENAME
                ,L.STATUS AS SETUP_STATUS
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
                ON APRV.EMPLOYEE_ID=RA.APPROVED_BY
                LEFT JOIN 
                (SELECT 
                WOD_ID AS ID
                ,LA.EMPLOYEE_ID
                ,NO_OF_DAYS
                ,WD.FROM_DATE||' - '||WD.TO_DATE AS SUB_NAME
                from 
                HRIS_EMPLOYEE_LEAVE_ADDITION LA
                JOIN Hris_Employee_Work_Dayoff WD ON (LA.WOD_ID=WD.ID)
                UNION
                SELECT 
                WOH_ID AS ID
                ,LA.EMPLOYEE_ID
                ,NO_OF_DAYS
                ,H.Holiday_Ename||'-'||WH.FROM_DATE||' - '||WH.TO_DATE AS SUB_NAME
                from 
                HRIS_EMPLOYEE_LEAVE_ADDITION LA
                JOIN Hris_Employee_Work_Holiday WH ON (LA.WOH_ID=WH.ID)
                LEFT JOIN Hris_Holiday_Master_Setup H ON (WH.HOLIDAY_ID=H.HOLIDAY_ID)) SLR ON (SLR.ID=LA.SUB_REF_ID AND SLR.EMPLOYEE_ID=LA.EMPLOYEE_ID),
                  HRIS_LEAVE_MONTH_CODE MTH,
                  HRIS_EMPLOYEE_LEAVE_ASSIGN ELA
                WHERE LA.ID = :id 
                AND TRUNC(LA.START_DATE) BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                AND LA.EMPLOYEE_ID            =ELA.EMPLOYEE_ID
                AND LA.LEAVE_ID               =ELA.LEAVE_ID
                AND (ELA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'N'  THEN mth.leave_year_month_no
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'Y'  THEN 
                    (SELECT LEAVE_YEAR_MONTH_NO FROM HRIS_LEAVE_MONTH_CODE
                    WHERE (
      select 
                       case when trunc(sysdate)>max(to_date) then
                        max(to_date)
                        else 
                        trunc(sysdate)
                        end
                        from HRIS_LEAVE_MONTH_CODE
                        ) BETWEEN FROM_DATE AND TO_DATE)
                  END
                OR ELA.FISCAL_YEAR_MONTH_NO IS NULL)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }

    public function fetchAttachmentsById($id){
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
      $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE LEAVE_ID = :id";
      $result = EntityHelper::rawQueryResult($this->adapter, $sql,$boundedParameter);
      return Helper::extractDbData($result);
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

    public function getAllCancelRequest($id) {
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $sql = "
                SELECT 
                  LA.ID                  AS ID,
                  LA.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
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
                  REC_APP_ROLE(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  )      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  ) AS YOUR_ROLE,
                  CASE WHEN ( ALR.R_A_ID IS NOT NULL OR ALA.R_A_ID  IS NOT NULL ) THEN 'SECONDARY' ELSE 'PRIMARY' END AS PRI_SEC
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
                LEFT JOIN HRIS_ALTERNATE_R_A ALR
                ON(ALR.R_A_FLAG='R' AND ALR.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALR.R_A_ID=:id)
                LEFT JOIN HRIS_ALTERNATE_R_A ALA
                ON(ALA.R_A_FLAG='A' AND ALA.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALA.R_A_ID=:id)
                -- CHANGES
                LEFT JOIN hris_rec_app_override RAO ON E.EMPLOYEE_ID=RAO.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES U
                ON(
                (
                (U.EMPLOYEE_ID   = RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID   =RA.APPROVED_BY
                OR U.EMPLOYEE_ID   =ALR.R_A_ID
                OR U.EMPLOYEE_ID   =ALA.R_A_ID)
               AND L.ENABLE_OVERRIDE='N' )
               OR
               (
                (U.EMPLOYEE_ID   = RAO.recommender
                OR U.EMPLOYEE_ID   =RAO.approver
               ) AND L.ENABLE_OVERRIDE='Y' 
               )
               
                )
                
             -- CHANGES
                
                
                WHERE E.STATUS        ='E'
                AND E.RETIRED_FLAG    ='N'
                AND ((
                (
                (
                (RA.RECOMMEND_BY= U.EMPLOYEE_ID)
                OR(ALR.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                ) OR (RAO.recommender=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='Y' )
                )
                AND LA.STATUS IN ('CP')) 
                OR (
                (
                (
                (RA.APPROVED_BY= U.EMPLOYEE_ID)
                OR(ALA.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                )
                OR ( RAO.APPROVER=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='N' )
                )
                AND LA.STATUS IN ('CR')) )
                AND U.EMPLOYEE_ID=:id
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL
                OR LA.STATUS IN ('CP','CR'))
                ORDER BY LA.REQUESTED_DT DESC";
				
				//echo $sql; die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function fetchByIdWithEmployeeId($id, $employeeId) {
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $boundedParameter['employeeId'] = $employeeId;
        
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
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAO.RECOMMENDER 
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE  ra.recommend_by END AS recommender_id,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAO.RECOMMENDER 
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE  ra.approved_by END AS approver_id,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAOR.FULL_NAME
                  WHEN ALR_E.FULL_NAME IS NOT NULL THEN ALR_E.FULL_NAME ELSE  recm.full_name END AS recommender_name,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAOR.FULL_NAME
                  WHEN ALA_E.FULL_NAME IS NOT NULL THEN ALA_E.FULL_NAME ELSE  aprv.full_name END AS approver_name,
                  ELA.TOTAL_DAYS                                      AS TOTAL_DAYS,
                  ELA.BALANCE                                         AS BALANCE
                  ,CASE WHEN SUB_REF_ID IS NOT NULL THEN 
INITCAP(L.LEAVE_ENAME)||'('||SLR.SUB_NAME||')' END AS LEAVE_ENAME
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
                ON APRV.EMPLOYEE_ID=RA.APPROVED_BY
                LEFT JOIN HRIS_ALTERNATE_R_A ALR ON(ALR.R_A_FLAG='R' AND ALR.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALR.R_A_ID=:employeeId)
                LEFT JOIN HRIS_ALTERNATE_R_A ALA ON(ALA.R_A_FLAG='A' AND ALA.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALA.R_A_ID=:employeeId)
                LEFT JOIN HRIS_EMPLOYEES ALR_E ON(ALR.R_A_ID=ALR_E.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES ALA_E ON(ALA.R_A_ID=ALA_E.EMPLOYEE_ID)
                LEFT JOIN hris_rec_app_override RAO ON E.EMPLOYEE_ID = RAO.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES RAOR ON (RAO.RECOMMENDER = RAOR.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES RAOA ON (RAO.RECOMMENDER = RAOA.EMPLOYEE_ID)
                LEFT JOIN 
                (SELECT 
                WOD_ID AS ID
                ,LA.EMPLOYEE_ID
                ,NO_OF_DAYS
                ,WD.FROM_DATE||' - '||WD.TO_DATE AS SUB_NAME
                from 
                HRIS_EMPLOYEE_LEAVE_ADDITION LA
                JOIN Hris_Employee_Work_Dayoff WD ON (LA.WOD_ID=WD.ID)
                UNION
                SELECT 
                WOH_ID AS ID
                ,LA.EMPLOYEE_ID
                ,NO_OF_DAYS
                ,H.Holiday_Ename||'-'||WH.FROM_DATE||' - '||WH.TO_DATE AS SUB_NAME
                from 
                HRIS_EMPLOYEE_LEAVE_ADDITION LA
                JOIN Hris_Employee_Work_Holiday WH ON (LA.WOH_ID=WH.ID)
                LEFT JOIN Hris_Holiday_Master_Setup H ON (WH.HOLIDAY_ID=H.HOLIDAY_ID)) SLR ON (SLR.ID=LA.SUB_REF_ID AND SLR.EMPLOYEE_ID=LA.EMPLOYEE_ID),
                  HRIS_LEAVE_MONTH_CODE MTH,
                  HRIS_EMPLOYEE_LEAVE_ASSIGN ELA
                WHERE LA.ID = :id
                AND TRUNC(LA.START_DATE) BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                AND LA.EMPLOYEE_ID            =ELA.EMPLOYEE_ID
                AND LA.LEAVE_ID               =ELA.LEAVE_ID
                AND (ELA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'N'  THEN mth.leave_year_month_no
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'Y'  THEN 
                    (SELECT LEAVE_YEAR_MONTH_NO FROM HRIS_LEAVE_MONTH_CODE
                    WHERE (
                    select 
                       case when trunc(sysdate)>max(to_date) then
                        max(to_date)
                        else 
                        trunc(sysdate)
                        end
                        from HRIS_LEAVE_MONTH_CODE
                    ) BETWEEN FROM_DATE AND TO_DATE)
                  END
                OR ELA.FISCAL_YEAR_MONTH_NO IS NULL)";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }
    
     public function getSameDateApprovedStatus($employeeId, $startDate, $endDate) {
         $boundedParameter = [];
         $boundedParameter['startDate'] = $startDate;
         $boundedParameter['endDate'] = $endDate;
         $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT COUNT(*) as LEAVE_COUNT
  FROM HRIS_EMPLOYEE_LEAVE_REQUEST
  WHERE ((:startDate BETWEEN START_DATE AND END_DATE)
  OR (:endDate BETWEEN START_DATE AND END_DATE))
  AND STATUS  IN ('AP','CP','CR')
  AND EMPLOYEE_ID = :employeeId
                ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }

}
