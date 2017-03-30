<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\TravelRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;

class TravelApproveRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TravelRequest::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getAllWidStatus($id,$status){
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[TravelRequest::TRAVEL_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE"),
            new Expression("TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY') AS TO_DATE"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.REFERENCE_TRAVEL_ID AS REFERENCE_TRAVEL_ID"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
             new Expression("INITCAP(TO_CHAR(TR.DEPARTURE_DATE, 'DD-MON-YYYY')) AS DEPARTURE_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RETURNED_DATE, 'DD-MON-YYYY')) AS RETURNED_DATE"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE") 
        ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=TR.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=TR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=TR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=TR.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN'=>'FIRST_NAME','RECM_MN'=>'MIDDLE_NAME','RECM_LN'=>'LAST_NAME'],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN'=>'FIRST_NAME','APRV_MN'=>'MIDDLE_NAME','APRV_LN'=>'LAST_NAME'],"left");

        $select->where([
            "TR.TRAVEL_ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
     public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT 
                    TR.TRAVEL_ID,
                    TR.TRAVEL_CODE,
                    TR.EMPLOYEE_ID,
                    TR.REQUESTED_AMOUNT,
                    TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                    TR.APPROVED_BY,
                    TR.RECOMMENDED_BY,
                    TR.PURPOSE,
                    TR.STATUS,
                    TR.REQUESTED_TYPE,
                    TR.REMARKS,
                    TR.RECOMMENDED_REMARKS,
                    TR.APPROVED_REMARKS,
                    TR.DESTINATION,
                    TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE,
                    TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY') AS TO_DATE,
                    TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                    TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                    E.FIRST_NAME,
                    E.MIDDLE_NAME,
                    E.LAST_NAME,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER
                    FROM HRIS_EMPLOYEE_TRAVEL_REQUEST TR
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.RECOMMEND_BY=".$id." AND TR.STATUS='RQ') OR (RA.APPROVED_BY=".$id." AND TR.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " AND TR.STATUS='RC' AND
                RA.RECOMMEND_BY=".$id;
        }else if($status=='AP'){
            $sql .= " AND TR.STATUS='AP' AND
                RA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" AND TR.STATUS='".$status."' AND
                ((RA.RECOMMEND_BY=".$id." AND TR.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=".$id." AND TR.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY TR.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
}