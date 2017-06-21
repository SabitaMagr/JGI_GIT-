<?php
namespace Loan\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;

class LoanStatusRepository implements RepositoryInterface{
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
        $loanId = $data['loanId'];
        $loanRequestStatusId = $data['loanRequestStatusId'];
        
        if($serviceEventTypeId==5 || $serviceEventTypeId==8 || $serviceEventTypeId==14){
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        }else{
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }
        
        $sql = "SELECT INITCAP(L.LOAN_NAME) AS LOAN_NAME,LR.REQUESTED_AMOUNT,
                INITCAP(TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY')) AS LOAN_DATE,
                INITCAP(TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                LR.STATUS AS STATUS,
                LR.EMPLOYEE_ID AS EMPLOYEE_ID,
                LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID,
                INITCAP(TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                INITCAP(TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                INITCAP(E.FULL_NAME) AS FULL_NAME,
                INITCAP(E.FIRST_NAME) AS FIRST_NAME,INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,INITCAP(E.LAST_NAME) AS LAST_NAME,
                INITCAP(E1.FIRST_NAME) AS FN1,INITCAP(E1.MIDDLE_NAME) AS MN1,INITCAP(E1.LAST_NAME) AS LN1,
                INITCAP(E2.FIRST_NAME) AS FN2,INITCAP(E2.MIDDLE_NAME) AS MN2,INITCAP(E2.LAST_NAME) AS LN2,
                RA.RECOMMEND_BY AS RECOMMENDER,
                RA.APPROVED_BY AS APPROVER,
                INITCAP(RECM.FIRST_NAME) AS RECM_FN,INITCAP(RECM.MIDDLE_NAME) AS RECM_MN,INITCAP(RECM.LAST_NAME) AS RECM_LN,
                INITCAP(APRV.FIRST_NAME) AS APRV_FN,INITCAP(APRV.MIDDLE_NAME) AS APRV_MN,INITCAP(APRV.LAST_NAME) AS APRV_LN,
                LR.RECOMMENDED_BY AS RECOMMENDED_BY,
                LR.APPROVED_BY AS APPROVED_BY,
                LR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                LR.APPROVED_REMARKS AS APPROVED_REMARKS
                FROM HRIS_EMPLOYEE_LOAN_REQUEST LR
                LEFT OUTER JOIN HRIS_LOAN_MASTER_SETUP L ON
                L.LOAN_ID=LR.LOAN_ID 
                LEFT OUTER JOIN HRIS_EMPLOYEES E ON
                E.EMPLOYEE_ID=LR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1 ON
                E1.EMPLOYEE_ID=LR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2 ON
                E2.EMPLOYEE_ID=LR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA ON
                LR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM ON
                RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV ON
                APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE 
                L.STATUS='E' AND
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
            if ($loanRequestStatusId != -1) {
                $sql .= " AND LR.STATUS ='" . $loanRequestStatusId . "'";
            }
        }
        if($recomApproveId!=null){
            if($loanRequestStatusId==-1){
                $sql .=" AND ((RA.RECOMMEND_BY=".$recomApproveId." AND  LR.STATUS='RQ') "
                        . "OR (LR.RECOMMENDED_BY=".$recomApproveId." AND (LR.STATUS='RC' OR LR.STATUS='R' OR LR.STATUS='AP')) "
                        . "OR (RA.APPROVED_BY=".$recomApproveId." AND  LR.STATUS='RC' ) "
                        . "OR (LR.APPROVED_BY=".$recomApproveId." AND (LR.STATUS='AP' OR (LR.STATUS='R' AND LR.APPROVED_DATE IS NOT NULL))) )";
            }else if($loanRequestStatusId=='RQ'){
                $sql .=" AND (RA.RECOMMEND_BY=".$recomApproveId." AND LR.STATUS='RQ')";
            }
            else if($loanRequestStatusId=='RC'){
                $sql .= " AND LR.STATUS='RC' AND
                    (LR.RECOMMENDED_BY=".$recomApproveId." OR RA.APPROVED_BY=".$recomApproveId.")";
            }else if($loanRequestStatusId=='AP'){
                $sql .= " AND LR.STATUS='AP' AND
                    (LR.RECOMMENDED_BY=".$recomApproveId." OR LR.APPROVED_BY=".$recomApproveId.")";
            }else if($loanRequestStatusId=='R'){
                $sql .=" AND LR.STATUS='".$loanRequestStatusId."' AND
                    ((LR.RECOMMENDED_BY=".$recomApproveId.") OR (LR.APPROVED_BY=".$recomApproveId." AND LR.APPROVED_DATE IS NOT NULL) )";
            }
        }
        
        if ($loanId != -1) {
            $sql .= " AND LR.LOAN_ID ='" . $loanId . "'";
        }
     
        if($fromDate!=null){
            $sql .= " AND LR.LOAN_DATE>=TO_DATE('".$fromDate."','DD-MM-YYYY')";
        }
        
        if($toDate!=null){   
            $sql .= "AND LR.LOAN_DATE<=TO_DATE('".$toDate."','DD-MM-YYYY')";
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
        
        $sql .=" ORDER BY LR.REQUESTED_DATE DESC";

        $statement = $this->adapter->query($sql);
       // print_r($statement->getSql());  die();
        $result = $statement->execute();
        return $result;
    }

}