<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\LoanRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Setup\Model\Loan;

class LoanApproveRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanRequest::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getAllWidStatus($id,$status){
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[LoanRequest::LOAN_REQUEST_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY') AS LOAN_DATE"),
            new Expression("TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("LR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LR.LOAN_ID AS LOAN_ID"),
            new Expression("LR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LR.APPROVED_BY AS APPROVED_BY"),
            new Expression("LR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LR.APPROVED_REMARKS AS APPROVED_REMARKS"),
        ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=LR.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left")
            ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=LR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
            ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=LR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left")
            ->join(['RA'=>"HR_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=LR.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HR_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN'=>'FIRST_NAME','RECM_MN'=>'MIDDLE_NAME','RECM_LN'=>'LAST_NAME'],"left")
            ->join(['APRV'=>"HR_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN'=>'FIRST_NAME','APRV_MN'=>'MIDDLE_NAME','APRV_LN'=>'LAST_NAME'],"left");

        $select->where([
            "LR.LOAN_REQUEST_ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
     public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT 
                    LR.LOAN_REQUEST_ID,
                    LR.REQUESTED_AMOUNT,
                    TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                    LR.APPROVED_BY,
                    LR.RECOMMENDED_BY,
                    LR.REASON,
                    LR.LOAN_ID,
                    LR.STATUS,
                    TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY') AS LOAN_DATE,
                    TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                    TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                    LR.EMPLOYEE_ID,
                    LR.RECOMMENDED_REMARKS,
                    LR.APPROVED_REMARKS,
                    E.FIRST_NAME,
                    E.MIDDLE_NAME,
                    E.LAST_NAME,
                    L.LOAN_NAME,
                    L.LOAN_CODE,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER
                    FROM HR_EMPLOYEE_LOAN_REQUEST LR
                    LEFT JOIN HR_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=LR.EMPLOYEE_ID
                    LEFT JOIN HR_LOAN_MASTER_SETUP L
                    ON LR.LOAN_ID=L.LOAN_ID
                    LEFT JOIN HR_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE L.STATUS = 'E' AND E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.RECOMMEND_BY=".$id." AND LR.STATUS='RQ') OR (RA.APPROVED_BY=".$id." AND LR.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " AND LR.STATUS='RC' AND
                RA.RECOMMEND_BY=".$id;
        }else if($status=='AP'){
            $sql .= " AND LR.STATUS='AP' AND
                RA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" AND LR.STATUS='".$status."' AND
                ((RA.RECOMMEND_BY=".$id." AND LR.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=".$id." AND LR.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY LR.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
}