<?php
namespace Advance\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;

class AdvanceStatusRepository implements RepositoryInterface{
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
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $advanceId = $data['advanceId'];
        $advanceRequestStatusId = $data['advanceRequestStatusId'];
        
        if($serviceEventTypeId==5 || $serviceEventTypeId==8 || $serviceEventTypeId==14){
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        }else{
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }
        
        $sql = "SELECT A.ADVANCE_NAME,A.ADVANCE_CODE,AR.REQUESTED_AMOUNT,
                TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY') AS ADVANCE_DATE,
                TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                AR.STATUS AS STATUS,
                AR.TERMS AS TERMS,
                AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID,
                TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                E.FIRST_NAME,E.MIDDLE_NAME,E.LAST_NAME,
                E1.FIRST_NAME AS FN1,E1.MIDDLE_NAME AS MN1,E1.LAST_NAME AS LN1,
                E2.FIRST_NAME AS FN2,E2.MIDDLE_NAME AS MN2,E2.LAST_NAME AS LN2,
                RA.RECOMMEND_BY AS RECOMMENDER,
                RA.APPROVED_BY AS APPROVER,
                RECM.FIRST_NAME AS RECM_FN,RECM.MIDDLE_NAME AS RECM_MN,RECM.LAST_NAME AS RECM_LN,
                APRV.FIRST_NAME AS APRV_FN,APRV.MIDDLE_NAME AS APRV_MN,APRV.LAST_NAME AS APRV_LN,
                AR.RECOMMENDED_BY AS RECOMMENDED_BY,
                AR.APPROVED_BY AS APPROVED_BY,
                AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                AR.APPROVED_REMARKS AS APPROVED_REMARKS
                FROM HR_EMPLOYEE_ADVANCE_REQUEST AR
                LEFT OUTER JOIN HR_ADVANCE_MASTER_SETUP A ON
                A.ADVANCE_ID=AR.ADVANCE_ID 
                LEFT OUTER JOIN HR_EMPLOYEES E ON
                E.EMPLOYEE_ID=AR.EMPLOYEE_ID
                LEFT OUTER JOIN HR_EMPLOYEES E1 ON
                E1.EMPLOYEE_ID=AR.RECOMMENDED_BY
                LEFT OUTER JOIN HR_EMPLOYEES E2 ON
                E2.EMPLOYEE_ID=AR.APPROVED_BY
                LEFT OUTER JOIN HR_RECOMMENDER_APPROVER RA ON
                AR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HR_EMPLOYEES RECM ON
                RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HR_EMPLOYEES APRV ON
                APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE 
                A.STATUS='E' AND
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
                    END OR  APRV.STATUS is null)";
        if($recomApproveId==null){
            if ($advanceRequestStatusId != -1) {
                $sql .= " AND AR.STATUS ='" . $advanceRequestStatusId . "'";
            }
        }
        if($recomApproveId!=null){
            if($advanceRequestStatusId==-1){
                $sql .=" AND ((RA.RECOMMEND_BY=".$recomApproveId." AND  AR.STATUS='RQ') "
                        . "OR (AR.RECOMMENDED_BY=".$recomApproveId." AND (AR.STATUS='RC' OR AR.STATUS='R' OR AR.STATUS='AP')) "
                        . "OR (RA.APPROVED_BY=".$recomApproveId." AND  AR.STATUS='RC' ) "
                        . "OR (AR.APPROVED_BY=".$recomApproveId." AND (AR.STATUS='AP' OR (AR.STATUS='R' AND AR.APPROVED_DATE IS NOT NULL))) )";
            }else if($advanceRequestStatusId=='RQ'){
                $sql .=" AND (RA.RECOMMEND_BY=".$recomApproveId." AND AR.STATUS='RQ')";
            }
            else if($advanceRequestStatusId=='RC'){
                $sql .= " AND AR.STATUS='RC' AND
                    (AR.RECOMMENDED_BY=".$recomApproveId." OR RA.APPROVED_BY=".$recomApproveId.")";
            }else if($advanceRequestStatusId=='AP'){
                $sql .= " AND AR.STATUS='AP' AND
                    (AR.RECOMMENDED_BY=".$recomApproveId." OR AR.APPROVED_BY=".$recomApproveId.")";
            }else if($advanceRequestStatusId=='R'){
                $sql .=" AND AR.STATUS='".$advanceRequestStatusId."' AND
                    ((AR.RECOMMENDED_BY=".$recomApproveId.") OR (AR.APPROVED_BY=".$recomApproveId." AND AR.APPROVED_DATE IS NOT NULL) )";
            }
        }
        
        if ($advanceId != -1) {
            $sql .= " AND AR.ADVANCE_ID ='" . $advanceId . "'";
        }
     
        if($fromDate!=null){
            $sql .= " AND AR.ADVANCE_DATE>=TO_DATE('".$fromDate."','DD-MM-YYYY')";
        }
        
        if($toDate!=null){   
            $sql .= " AND AR.ADVANCE_DATE<=TO_DATE('".$toDate."','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
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
        
        $sql .=" ORDER BY AR.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
       // print_r($statement->getSql());  die();
        $result = $statement->execute();
        return $result;
    }

}