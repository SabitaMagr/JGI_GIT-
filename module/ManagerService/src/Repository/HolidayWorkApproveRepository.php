<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\WorkOnHoliday;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;

class HolidayWorkApproveRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(WorkOnHoliday::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getAllWidStatus($id,$status){
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[WorkOnHoliday::ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WH.ID AS ID"),
            new Expression("WH.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("WH.HOLIDAY_ID AS HOLIDAY_ID") ,
            new Expression("TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("WH.DURATION AS DURATION"),
            new Expression("WH.REMARKS AS REMARKS"),
            new Expression("WH.STATUS AS STATUS"),
            new Expression("WH.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("WH.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WH.APPROVED_BY AS APPROVED_BY"),
            new Expression("TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("WH.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY') AS MODIFIED_DATE"), 
        ], true);

        $select->from(['WH' => WorkOnHoliday::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=WH.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=WH.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=WH.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=WH.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN'=>'FIRST_NAME','RECM_MN'=>'MIDDLE_NAME','RECM_LN'=>'LAST_NAME'],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN'=>'FIRST_NAME','APRV_MN'=>'MIDDLE_NAME','APRV_LN'=>'LAST_NAME'],"left");

        $select->where([
            "WH.ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
     public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT 
                    WH.ID,
                    WH.EMPLOYEE_ID,
                    TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE,
                    WH.APPROVED_BY,
                    WH.RECOMMENDED_BY,
                    WH.REMARKS,
                    WH.DURATION,
                    WH.STATUS,
                    WH.HOLIDAY_ID,
                    WH.RECOMMENDED_REMARKS,
                    WH.APPROVED_REMARKS,
                    TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE,
                    TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY') AS TO_DATE,
                    TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE,
                    TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE,
                    TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY') AS MODIFIED_DATE,
                    E.FIRST_NAME,
                    E.MIDDLE_NAME,
                    E.LAST_NAME,
                    H.HOLIDAY_ENAME,
                    H.HOLIDAY_LNAME,
                    H.HOLIDAY_CODE,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER
                    FROM HRIS_EMPLOYEE_WORK_HOLIDAY WH
                    LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP H ON 
                    H.HOLIDAY_ID=WH.HOLIDAY_ID
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=WH.EMPLOYEE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE  H.STATUS = 'E' AND  E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.RECOMMEND_BY=".$id." AND WH.STATUS='RQ') OR (RA.APPROVED_BY=".$id." AND WH.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " AND WH.STATUS='RC' AND
                RA.RECOMMEND_BY=".$id;
        }else if($status=='AP'){
            $sql .= " AND WH.STATUS='AP' AND
                RA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" AND WH.STATUS='".$status."' AND
                ((RA.RECOMMEND_BY=".$id." AND WH.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=".$id." AND WH.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY WH.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
}