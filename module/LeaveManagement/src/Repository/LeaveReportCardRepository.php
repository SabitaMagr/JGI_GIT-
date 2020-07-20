<?php
namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveApply;
use Setup\Model\HrEmployees;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql; 

class LeaveReportCardRepository extends HrisRepository {

  public function fetchLeaveReportCard($by){
    $leaveId = $by['data']['leaveId'];
    $leaveIdFilter = "";
    $boundedParameter = [];
    if($leaveId != '' && $leaveId != null){
        $leaveData=$this->getBoundedForArray($leaveId,'leaveId');
        $boundedParameter=array_merge($boundedParameter,$leaveData['parameter']);
        $leaveIdFilter = " and l.leave_id IN ({$leaveData['sql']})";
    }
    
    $employees = $by['data']['employeeId'];
    $boundedParameter['employees']=$employees;
    //$employees = implode(',', $employees);
    
    $leaveYear = $by['data']['leaveYear'];
    
    if ($leaveYear != null) {
            $boundedParameter['leaveYear']=$leaveYear;
            $leaveYearStatusCondition = "( ( L.STATUS ='E' OR L.OLD_LEAVE='Y' ) AND L.LEAVE_YEAR= :leaveYear )";
        } else {
            $leaveYearStatusCondition = "L.STATUS ='E'";
        }
    

    $sql = "(SELECT LA.ID AS ID, E.EMPLOYEE_CODE AS EMPLOYEE_ID, E.EMPLOYEE_CODE AS 
    EMPLOYEE_CODE,E.JOIN_DATE AS JOIN_DATE, LA.LEAVE_ID AS LEAVE_ID, 
    (CASE WHEN  E.ADDR_PERM_STREET_ADDRESS IS NULL THEN '-' ELSE E.ADDR_PERM_STREET_ADDRESS END) AS ADDR_PERM_STREET_ADDRESS,
    (CASE WHEN  E.ADDR_TEMP_STREET_ADDRESS IS NULL THEN '-' ELSE E.ADDR_TEMP_STREET_ADDRESS END) AS ADDR_TEMP_STREET_ADDRESS,
    D.DESIGNATION_TITLE AS DESIGNATION_TITLE,HD.DEPARTMENT_NAME AS DEPARTMENT,
    INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD, BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) 
    AS FROM_DATE_BS, INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD, BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) 
    AS TO_DATE_BS, LA.HALF_DAY AS HALF_DAY, (CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') THEN 'Full Day' WHEN (LA.HALF_DAY = 'F') 
    THEN 'First Half' ELSE 'Second Half' END) AS HALF_DAY_DETAIL, LA.GRACE_PERIOD AS GRACE_PERIOD, (CASE WHEN LA.GRACE_PERIOD = 'E' 
    THEN 'Early' WHEN LA.GRACE_PERIOD = 'L' THEN 'Late' ELSE '-' END) AS GRACE_PERIOD_DETAIL, LA.NO_OF_DAYS AS NO_OF_DAYS, 
    INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD, BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))
    AS REQUESTED_DT_BS, (CASE WHEN LA.REMARKS IS null THEN '-' ELSE LA.REMARKS END) AS REMARKS, to_char(LA.STATUS)                                          AS STATUS,
  to_char(LEAVE_STATUS_DESC(LA.STATUS))                       AS STATUS_DETAIL,
  to_char(LA.RECOMMENDED_BY)                                  AS RECOMMENDED_BY,
  INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT,
  to_char(LA.RECOMMENDED_REMARKS)                             AS RECOMMENDED_REMARKS,
  to_char(LA.APPROVED_BY)                                     AS APPROVED_BY,
  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))    AS APPROVED_DT,
  to_char(LA.APPROVED_REMARKS)                                AS APPROVED_REMARKS,
    (CASE WHEN LA.STATUS = 'XX' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT, (CASE WHEN LA.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS 
    ALLOW_DELETE, L.LEAVE_CODE AS LEAVE_CODE, INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME, INITCAP(E.FULL_NAME) AS FULL_NAME, 
    INITCAP(E2.FULL_NAME) AS RECOMMENDED_BY_NAME, INITCAP(E3.FULL_NAME) AS APPROVED_BY_NAME, RA.RECOMMEND_BY AS RECOMMENDER_ID, 
    RA.APPROVED_BY AS APPROVER_ID, INITCAP(RECM.FULL_NAME) AS RECOMMENDER_NAME, INITCAP(APRV.FULL_NAME) AS APPROVER_NAME 
    FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA INNER JOIN HRIS_LEAVE_MASTER_SETUP  L ON L.LEAVE_ID=LA.LEAVE_ID LEFT JOIN 
    HRIS_EMPLOYEES  E ON LA.EMPLOYEE_ID=E.EMPLOYEE_ID LEFT JOIN HRIS_EMPLOYEES  E2 ON 
    E2.EMPLOYEE_ID=LA.RECOMMENDED_BY LEFT JOIN HRIS_EMPLOYEES  E3 ON E3.EMPLOYEE_ID=LA.APPROVED_BY LEFT JOIN 
    HRIS_RECOMMENDER_APPROVER  RA ON RA.EMPLOYEE_ID=LA.EMPLOYEE_ID LEFT JOIN HRIS_EMPLOYEES  RECM ON 
    RECM.EMPLOYEE_ID=RA.RECOMMEND_BY LEFT JOIN HRIS_EMPLOYEES APRV ON APRV.EMPLOYEE_ID=RA.APPROVED_BY 
    LEFT JOIN HRIS_DESIGNATIONS D ON E.DESIGNATION_ID = D.DESIGNATION_ID  
    LEFT JOIN HRIS_DEPARTMENTS HD ON E.DEPARTMENT_ID = HD.DEPARTMENT_ID
    WHERE {$leaveYearStatusCondition} and la.status in ( 'AP','CP','CR')  AND E.EMPLOYEE_ID IN (:employees) {$leaveIdFilter}"
    . " union


  SELECT 0     AS ID,
  E.EMPLOYEE_CODE AS EMPLOYEE_ID,
  E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
  E.JOIN_DATE     AS JOIN_DATE,
  LA.LEAVE_ID     AS LEAVE_ID,
  (
  CASE
    WHEN E.ADDR_PERM_STREET_ADDRESS IS NULL
    THEN '-'
    ELSE E.ADDR_PERM_STREET_ADDRESS
  END) AS ADDR_PERM_STREET_ADDRESS,
  (
  CASE
    WHEN E.ADDR_TEMP_STREET_ADDRESS IS NULL
    THEN '-'
    ELSE E.ADDR_TEMP_STREET_ADDRESS
  END)                                           AS ADDR_TEMP_STREET_ADDRESS,
  D.DESIGNATION_TITLE                            AS DESIGNATION_TITLE,
  HD.DEPARTMENT_NAME                             AS DEPARTMENT,
  INITCAP(TO_CHAR(LA.ATTENDANCE_DT, 'DD-MON-YYYY')) AS FROM_DATE_AD,
  '' AS FROM_DATE_BS,
  INITCAP(TO_CHAR(LA.ATTENDANCE_DT, 'DD-MON-YYYY'))   AS TO_DATE_AD,
  ''   AS TO_DATE_BS,
  ''                                    AS HALF_DAY,
  ''            AS HALF_DAY_DETAIL,
  ''       AS GRACE_PERIOD,
  ''                                             AS GRACE_PERIOD_DETAIL,
  LA.NO_OF_DAYS                                    AS NO_OF_DAYS,
  INITCAP(TO_CHAR(LA.ATTENDANCE_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD,
  '' AS REQUESTED_DT_BS,
  (
  CASE
    WHEN LA.REMARKS IS NULL
    THEN '-'
    ELSE LA.REMARKS
  END)                                               AS REMARKS,
  ''                                          AS STATUS,
  ''                       AS STATUS_DETAIL,
  ''                                  AS RECOMMENDED_BY,
  '',
  ''                             AS RECOMMENDED_REMARKS,
  ''                                    AS APPROVED_BY,
  ''    AS APPROVED_DT,
  ''                                AS APPROVED_REMARKS,
  'N' AS ALLOW_EDIT,
  'N'                    AS ALLOW_DELETE,
  L.LEAVE_CODE            AS LEAVE_CODE,
  INITCAP(L.LEAVE_ENAME)  AS LEAVE_ENAME,
  INITCAP(E.FULL_NAME)    AS FULL_NAME,
  'Deduction'   AS RECOMMENDED_BY_NAME,
  'Deduction'   AS APPROVED_BY_NAME,
  1         AS RECOMMENDER_ID,
  1          AS APPROVER_ID,
  '' AS RECOMMENDER_NAME,
  '' AS APPROVER_NAME
FROM HRIS_EMPLOYEE_PENALTY_DAYS LA
INNER JOIN HRIS_LEAVE_MASTER_SETUP L
ON L.LEAVE_ID=LA.LEAVE_ID
LEFT JOIN HRIS_EMPLOYEES E
ON LA.EMPLOYEE_ID=E.EMPLOYEE_ID
LEFT JOIN HRIS_DESIGNATIONS D
ON E.DESIGNATION_ID = D.DESIGNATION_ID
LEFT JOIN HRIS_DEPARTMENTS HD
ON E.DEPARTMENT_ID = HD.DEPARTMENT_ID
where {$leaveYearStatusCondition} and E.EMPLOYEE_ID IN (:employees) {$leaveIdFilter})
ORDER BY REQUESTED_DT_AD ASC
";  
//                            echo $sql; die;
    return $this->rawQuery($sql,$boundedParameter);    
  }

  public function fetchLeaves($empId, $leaveId,$leaveYear){
      $boundedParameter = [];
    $leaveIdFilter = "";
    if($leaveId != '' && $leaveId != null){
        $leaveData=$this->getBoundedForArray($leaveId,'leaveId');
        $boundedParameter=array_merge($boundedParameter,$leaveData['parameter']);
        $leaveIdFilter = " and lms.leave_id IN ({$leaveData['sql']})";
    }
    
    
    if ($leaveYear != null) {
        $boundedParameter['leaveYear']=$leaveYear;
            $leaveYearStatusCondition = "( ( lms.STATUS ='E' OR lms.OLD_LEAVE='Y' ) AND lms.LEAVE_YEAR= :leaveYear )";
        } else {
            $leaveYearStatusCondition = "lms.STATUS ='E'";
        }
        
        $boundedParameter['empId']=$empId;
    
    $sql = "select 
    Lms.Leave_Ename,Lms.LEAVE_ID,
    la.Total_Days - 
    case when lms.is_monthly='Y' 
    and lms.CARRY_FORWARD='Y' then nvl(la.PREVIOUS_YEAR_BAL, 0) else 0 end as total_days, nvl(la.PREVIOUS_YEAR_BAL, 0) as PREVIOUS_YEAR_BAL,
    la.Total_Days + case when lms.is_monthly='Y' then 0 else nvl(la.PREVIOUS_YEAR_BAL, 0) end as Balance
    from hris_leave_master_setup lms
    left join Hris_Employee_Leave_Assign la on (lms.leave_id=la.leave_id )
    where {$leaveYearStatusCondition} and la.employee_id= :empId 
        and
    (la.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN lms.is_monthly = 'Y'  THEN 
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
                OR la.FISCAL_YEAR_MONTH_NO IS NULL)
    {$leaveIdFilter}
    order by Lms.VIEW_ORDER asc";
//    echo $sql; die;
    return $this->rawQuery($sql,$boundedParameter);
  }
} 
