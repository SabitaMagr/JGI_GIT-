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
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
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
            new Expression("TR.APPROVER_ROLE AS APPROVER_ROLE"),
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
            new Expression("TR.ADVANCE_AMOUNT AS ADVANCE_AMOUNT"),
            new Expression("INITCAP(TO_CHAR(TR.DEPARTURE_DATE, 'DD-MON-YYYY')) AS DEPARTURE_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RETURNED_DATE, 'DD-MON-YYYY')) AS RETURNED_DATE"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE") 
        ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=TR.EMPLOYEE_ID",["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=TR.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=TR.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=TR.EMPLOYEE_ID",['RECOMMENDER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN' =>  new Expression("INITCAP(RECM.FIRST_NAME)"), 'RECM_MN' => new Expression("INITCAP(RECM.MIDDLE_NAME)"), 'RECM_LN' => new Expression("INITCAP(RECM.LAST_NAME)")],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN' =>  new Expression("INITCAP(APRV.FIRST_NAME)"), 'APRV_MN' => new Expression("INITCAP(APRV.MIDDLE_NAME)"), 'APRV_LN' => new Expression("INITCAP(APRV.LAST_NAME)")],"left")
            ->join(['TS'=>"HRIS_TRAVEL_SUBSTITUTE"],"TS.TRAVEL_ID=TR.TRAVEL_ID",['SUB_EMPLOYEE_ID'=>'EMPLOYEE_ID','SUB_APPROVED_DATE'=>new Expression("INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY'))"),'SUB_REMARKS'=>"REMARKS",'SUB_APPROVED_FLAG'=>"APPROVED_FLAG"],"left");

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
                    INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    TR.APPROVED_BY,
                    TR.RECOMMENDED_BY,
                    TR.PURPOSE,
                    TR.STATUS,
                    TR.REQUESTED_TYPE,
                    TR.REMARKS,
                    TR.RECOMMENDED_REMARKS,
                    TR.APPROVED_REMARKS,
                    TR.DESTINATION,
                    TR.APPROVER_ROLE,
                    TR.ADVANCE_AMOUNT,
                    INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE,
                    INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE,
                    INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    INITCAP(E.FULL_NAME) AS FULL_NAME,
                    RA.APPROVED_BY AS RECOMMENDER,
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
                    END)  AS APPROVER,
                    TS.APPROVED_FLAG AS APPROVED_FLAG,
                    INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY')) AS SUB_APPROVED_DATE,
                    TS.EMPLOYEE_ID AS SUB_EMPLOYEE_ID
                    FROM HRIS_EMPLOYEE_TRAVEL_REQUEST TR
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    LEFT JOIN HRIS_TRAVEL_SUBSTITUTE TS
                    ON TS.TRAVEL_ID = TR.TRAVEL_ID
                    WHERE E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.APPROVED_BY=".$id." AND TR.STATUS='RQ'"
                    . " AND
                    (TS.APPROVED_FLAG = CASE WHEN TS.EMPLOYEE_ID IS NOT NULL
                         THEN ('Y')     
                    END OR  TS.EMPLOYEE_ID is null)) OR (
                    ('$id'       =
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
                    END)AND TR.STATUS='RC') )";
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
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
}