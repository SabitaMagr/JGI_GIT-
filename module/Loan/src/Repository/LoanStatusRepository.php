<?php
namespace Loan\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;

class LoanStatusRepository implements RepositoryInterface{
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        
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
        $loanId = $data['loanId'];
        $loanRequestStatusId = $data['loanRequestStatusId'];
        
        if($serviceEventTypeId==5 || $serviceEventTypeId==8 || $serviceEventTypeId==14){
            $retiredFlag = " AND E.RETIRED_FLAG='Y' ";
        }else{
            $retiredFlag = " AND E.RETIRED_FLAG='N' ";
        }
        
        $sql = "SELECT L.LOAN_NAME,LR.REQUESTED_AMOUNT,
                TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY') AS LOAN_DATE,
                TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                LR.STATUS AS STATUS,
                LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID,
                TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                E.FIRST_NAME,E.MIDDLE_NAME,E.LAST_NAME,
                E1.FIRST_NAME AS FN1,E1.MIDDLE_NAME AS MN1,E1.LAST_NAME AS LN1,
                E2.FIRST_NAME AS FN2,E2.MIDDLE_NAME AS MN2,E2.LAST_NAME AS LN2,
                LA.RECOMMENDED_BY AS RECOMMENDER,
                LA.APPROVED_BY AS APPROVER,
                LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                LA.APPROVED_REMARKS AS APPROVED_REMARKS
                FROM HR_EMPLOYEE_LEAVE_REQUEST LA
                LEFT OUTER JOIN HR_LEAVE_MASTER_SETUP L ON
                L.LEAVE_ID=LA.LEAVE_ID 
                LEFT OUTER JOIN HR_EMPLOYEES E ON
                E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT OUTER JOIN HR_EMPLOYEES E1 ON
                E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT OUTER JOIN HR_EMPLOYEES E2 ON
                E2.EMPLOYEE_ID=LA.APPROVED_BY
                WHERE 
                L.STATUS='E' AND
                E.STATUS='E'".$retiredFlag."              
                AND
                (E1.STATUS = CASE WHEN E1.STATUS IS NOT NULL
                         THEN ('E')     
                    END OR  E1.STATUS is null) AND
                (E2.STATUS = CASE WHEN E2.STATUS IS NOT NULL
                         THEN ('E')       
                    END OR  E2.STATUS is null)";
        if($recomApproveId==null){
            if ($leaveRequestStatusId != -1) {
                $sql .= " AND LA.STATUS ='" . $leaveRequestStatusId . "'";
            }
        }
        if($recomApproveId!=null){
            if($leaveRequestStatusId==-1){
                $sql .=" AND ((LA.RECOMMENDED_BY=".$recomApproveId." AND  ( LA.STATUS='RQ' OR LA.STATUS='RC' OR (LA.STATUS='R' AND LA.APPROVED_DT IS NULL))) OR (LA.APPROVED_BY=".$recomApproveId." AND ( LA.STATUS='RC' OR LA.STATUS='AP' OR (LA.STATUS='R' AND LA.APPROVED_DT IS NOT NULL))) )";
            }else if($leaveRequestStatusId=='RQ'){
                $sql .=" AND ((LA.RECOMMENDED_BY=".$recomApproveId." AND LA.STATUS='RQ') OR (LA.APPROVED_BY=".$recomApproveId." AND LA.STATUS='RC') )";
            }
            else if($leaveRequestStatusId=='RC'){
                $sql .= " AND LA.STATUS='RC' AND
                    LA.RECOMMENDED_BY=".$recomApproveId;
            }else if($leaveRequestStatusId=='AP'){
                $sql .= " AND LA.STATUS='AP' AND
                    LA.APPROVED_BY=".$recomApproveId;
            }else if($leaveRequestStatusId=='R'){
                $sql .=" AND LA.STATUS='".$leaveRequestStatusId."' AND
                    ((LA.RECOMMENDED_BY=".$recomApproveId." AND LA.APPROVED_DT IS NULL) OR (LA.APPROVED_BY=".$recomApproveId." AND LA.APPROVED_DT IS NOT NULL) )";
            }
        }
        
        if ($leaveId != -1) {
            $sql .= " AND LA.LEAVE_ID ='" . $leaveId . "'";
        }
     
        if($fromDate!=null){
            $sql .= " AND LA.START_DATE>=TO_DATE('".$fromDate."','DD-MM-YYYY')";
        }
        
        if($toDate!=null){   
            $sql .= "AND LA.END_DATE<=TO_DATE('".$toDate."','DD-MM-YYYY')";
        }

        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
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
        
        $sql .=" ORDER BY LA.REQUESTED_DT DESC";

        $statement = $this->adapter->query($sql);
        //print_r($statement->getSql()); 
        $result = $statement->execute();
        return $result;
    }

}