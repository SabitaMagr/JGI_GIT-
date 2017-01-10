<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\AdvanceRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Setup\Model\Advance;

class AdvanceApproveRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AdvanceRequest::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getAllWidStatus($id,$status){
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[AdvanceRequest::ADVANCE_REQUEST_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY') AS ADVANCE_DATE"),
            new Expression("TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("AR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("AR.REASON AS REASON"),
            new Expression("AR.TERMS AS TERMS"),
            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AR.ADVANCE_ID AS ADVANCE_ID"),
            new Expression("AR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("AR.APPROVED_BY AS APPROVED_BY"),
            new Expression("AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
        ], true);

        $select->from(['AR' => AdvanceRequest::TABLE_NAME])
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=AR.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left")
            ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=AR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
            ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=AR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left")
            ->join(['RA'=>"HR_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=AR.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HR_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN'=>'FIRST_NAME','RECM_MN'=>'MIDDLE_NAME','RECM_LN'=>'LAST_NAME'],"left")
            ->join(['APRV'=>"HR_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN'=>'FIRST_NAME','APRV_MN'=>'MIDDLE_NAME','APRV_LN'=>'LAST_NAME'],"left");

        $select->where([
            "AR.ADVANCE_REQUEST_ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
     public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT 
                    AR.ADVANCE_REQUEST_ID,
                    AR.EMPLOYEE_ID,
                    AR.REQUESTED_AMOUNT,
                    TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                    AR.APPROVED_BY,
                    AR.RECOMMENDED_BY,
                    AR.REASON,
                    AR.ADVANCE_ID,
                    AR.STATUS,
                    TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY') AS ADVANCE_DATE,
                    TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                    TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                    AR.EMPLOYEE_ID,
                    E.FIRST_NAME,
                    E.MIDDLE_NAME,
                    E.LAST_NAME,
                    A.ADVANCE_NAME,
                    A.ADVANCE_CODE,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER
                    FROM HR_EMPLOYEE_ADVANCE_REQUEST AR
                    LEFT JOIN HR_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=AR.EMPLOYEE_ID
                    LEFT JOIN HR_ADVANCE_MASTER_SETUP L
                    ON AR.ADVANCE_ID=A.ADVANCE_ID
                    LEFT JOIN HR_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE L.STATUS = 'E' AND E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.RECOMMEND_BY=".$id." AND AR.STATUS='RQ') OR (RA.APPROVED_BY=".$id." AND AR.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " AND AR.STATUS='RC' AND
                RA.RECOMMEND_BY=".$id;
        }else if($status=='AP'){
            $sql .= " AND AR.STATUS='AP' AND
                RA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" AND AR.STATUS='".$status."' AND
                ((RA.RECOMMEND_BY=".$id." AND AR.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=".$id." AND AR.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY AR.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
}