<?php
namespace Travel\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;

class TravelStatusRepository implements RepositoryInterface{
    private $adapter;
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function getFilteredRecord($data,$recomApproveId=null){
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
        $travelRequestStatusId = $data['travelRequestStatusId'];
        
        if($serviceEventTypeId==5 || $serviceEventTypeId==8 || $serviceEventTypeId==14){
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        }else{
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }
        
        $sql = "SELECT TR.REQUESTED_AMOUNT,
                INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE,
                INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE,
                INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                TR.STATUS AS STATUS,
                TR.TRAVEL_ID AS TRAVEL_ID,
                TR.TRAVEL_CODE AS TRAVEL_CODE,
                TR.REMARKS AS REMARKS,
                TR.REQUESTED_TYPE AS REQUESTED_TYPE,
                TR.DESTINATION AS DESTINATION,
                TR.PURPOSE AS PURPOSE,
                TR.EMPLOYEE_ID AS EMPLOYEE_ID,
                TR.APPROVER_ROLE AS APPROVER_ROLE,
                TR.ADVANCE_AMOUNT AS ADVANCE_AMOUNT,
                INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                INITCAP(E.FULL_NAME) AS FULL_NAME,
                INITCAP(E.FIRST_NAME) AS FIRST_NAME,INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,INITCAP(E.LAST_NAME) AS LAST_NAME,
                INITCAP(E1.FIRST_NAME) AS FN1,INITCAP(E1.MIDDLE_NAME) AS MN1,INITCAP(E1.LAST_NAME) AS LN1,
                INITCAP(E2.FIRST_NAME) AS FN2,INITCAP(E2.MIDDLE_NAME) AS MN2,INITCAP(E2.LAST_NAME) AS LN2,
                RA.APPROVED_BY  AS RECOMMENDER,
                (
                CASE
                  WHEN TR.APPROVER_ROLE='DCEO'
                  THEN
                    (SELECT EMPLOYEE_ID
                    FROM HRIS_EMPLOYEES
                    WHERE IS_DCEO   ='Y'
                    AND STATUS      ='E'
                    AND RETIRED_FLAG='N'
                    )
                  ELSE
                    (SELECT EMPLOYEE_ID
                    FROM HRIS_EMPLOYEES
                    WHERE IS_CEO    ='Y'
                    AND STATUS      ='E'
                    AND RETIRED_FLAG='N'
                    )
                END) AS APPROVER,
                INITCAP(RECM.FIRST_NAME) AS RECM_FN,INITCAP(RECM.MIDDLE_NAME) AS RECM_MN,INITCAP(RECM.LAST_NAME) AS RECM_LN,
                INITCAP(APRV.FIRST_NAME) AS APRV_FN,INITCAP(APRV.MIDDLE_NAME) AS APRV_MN,INITCAP(APRV.LAST_NAME) AS APRV_LN,
                TR.RECOMMENDED_BY AS RECOMMENDED_BY,
                TR.APPROVED_BY AS APPROVED_BY,
                TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                TR.APPROVED_REMARKS AS APPROVED_REMARKS,
                TS.APPROVED_FLAG AS SUB_APPROVED_FLAG,
                INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY')) AS SUB_APPROVED_DATE,
                TS.EMPLOYEE_ID AS SUB_EMPLOYEE_ID
                FROM HRIS_EMPLOYEE_TRAVEL_REQUEST TR
                LEFT OUTER JOIN HRIS_EMPLOYEES E ON
                E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1 ON
                E1.EMPLOYEE_ID=TR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2 ON
                E2.EMPLOYEE_ID=TR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA ON
                TR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM ON
                RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV ON
                APRV.EMPLOYEE_ID = RA.APPROVED_BY
                LEFT OUTER JOIN HRIS_TRAVEL_SUBSTITUTE TS
                ON TR.TRAVEL_ID = TS.TRAVEL_ID
                WHERE 
                E.STATUS='E'".$retiredFlag."              
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
                    END OR  APRV.STATUS is null)
                    AND (TS.APPROVED_FLAG = CASE WHEN TS.EMPLOYEE_ID IS NOT NULL
                         THEN ('Y')     
                    END OR  TS.EMPLOYEE_ID is null)";
        if($recomApproveId==null){
            if ($travelRequestStatusId != -1) {
                $sql .= " AND TR.STATUS ='" . $travelRequestStatusId . "'";
            }
        }
        if($recomApproveId!=null){
            if($travelRequestStatusId==-1){
                $sql .=" AND ((RA.APPROVED_BY=".$recomApproveId." AND  TR.STATUS='RQ') "
                        . "OR (TR.RECOMMENDED_BY=".$recomApproveId." AND (TR.STATUS='RC' OR TR.STATUS='R' OR TR.STATUS='AP' OR TR.STATUS='SC')) "
                        . "OR ((".$recomApproveId."=
                            CASE
                              WHEN TR.APPROVER_ROLE='DCEO'
                              THEN
                                (SELECT EMPLOYEE_ID
                                FROM HRIS_EMPLOYEES
                                WHERE IS_DCEO   ='Y'
                                AND STATUS      ='E'
                                AND RETIRED_FLAG='N'
                                )
                              ELSE
                                (SELECT EMPLOYEE_ID
                                FROM HRIS_EMPLOYEES
                                WHERE IS_CEO    ='Y'
                                AND STATUS      ='E'
                                AND RETIRED_FLAG='N'
                                )
                            END) AND  TR.STATUS='RC' ) "
                        . "OR (TR.APPROVED_BY=".$recomApproveId." AND (TR.STATUS='AP' OR TR.STATUS='SC' OR (TR.STATUS='R' AND TR.APPROVED_DATE IS NOT NULL))) )";
            }else if($travelRequestStatusId=='RQ'){
                $sql .=" AND (RA.RECOMMEND_BY=".$recomApproveId." AND TR.STATUS='RQ')";
            }
            else if($travelRequestStatusId=='RC'){
                $sql .= " AND TR.STATUS='RC' AND
                    (TR.RECOMMENDED_BY=".$recomApproveId." OR RA.APPROVED_BY=".$recomApproveId.")";
            }else if($travelRequestStatusId=='AP'){
                $sql .= " AND TR.STATUS='AP' AND
                    (TR.RECOMMENDED_BY=".$recomApproveId." OR TR.APPROVED_BY=".$recomApproveId.")";
            }else if($travelRequestStatusId=='R'){
                $sql .=" AND TR.STATUS='".$travelRequestStatusId."' AND
                    ((TR.RECOMMENDED_BY=".$recomApproveId.") OR (TR.APPROVED_BY=".$recomApproveId." AND TR.APPROVED_DATE IS NOT NULL) )";
            }
        }
        if($fromDate!=null){
            $sql .= " AND TR.FROM_DATE>=TO_DATE('".$fromDate."','DD-MM-YYYY')";
        }
        
        if($toDate!=null){   
            $sql .= "AND TR.TO_DATE<=TO_DATE('".$toDate."','DD-MM-YYYY')";
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
        
        $sql .=" ORDER BY TR.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
//        print_r($statement->getSql());  die();
        $result = $statement->execute();
        return $result;
    }

}