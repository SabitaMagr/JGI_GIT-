<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\Overtime;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Application\Helper\EntityHelper;

class OvertimeApproveRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Overtime::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getAllWidStatus($id,$status){
        
    }

    public function edit(Model $model, $id) {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[Overtime::OVERTIME_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Overtime::class, null, [Overtime::OVERTIME_DATE,Overtime::REQUESTED_DATE, Overtime::RECOMMENDED_DATE, Overtime::APPROVED_DATE, Overtime::MODIFIED_DATE], NULL, NULL, NULL, "OT"), false);

        $select->from(['OT' => Overtime::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=OT.". Overtime::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=OT.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=OT.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")],"left")
                ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=OT.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
                ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN' =>  new Expression("INITCAP(RECM.FIRST_NAME)"), 'RECM_MN' => new Expression("INITCAP(RECM.MIDDLE_NAME)"), 'RECM_LN' => new Expression("INITCAP(RECM.LAST_NAME)")],"left")
                ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN' =>  new Expression("INITCAP(APRV.FIRST_NAME)"), 'APRV_MN' => new Expression("INITCAP(APRV.MIDDLE_NAME)"), 'APRV_LN' => new Expression("INITCAP(APRV.LAST_NAME)")],"left");

        $select->where([
            "OT.OVERTIME_ID=".$id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
     public function getAllRequest($id = null,$status=null)
    {
        $sql = "SELECT 
                    OT.OVERTIME_ID,
                    OT.EMPLOYEE_ID,
                    INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE,
                    INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    OT.APPROVED_BY,
                    OT.RECOMMENDED_BY,
                    OT.REMARKS,
                    OT.DESCRIPTION,
                    OT.STATUS,
                    OT.RECOMMENDED_REMARKS,
                    OT.APPROVED_REMARKS,
                    INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    INITCAP(TO_CHAR(OT.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER
                    FROM HRIS_OVERTIME OT
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=OT.EMPLOYEE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE  E.STATUS='E'
                    AND E.RETIRED_FLAG='N'";
        if($status==null){
            $sql .=" AND ((RA.RECOMMEND_BY=".$id." AND OT.STATUS='RQ') OR (RA.APPROVED_BY=".$id." AND OT.STATUS='RC') )";
        }else if($status=='RC'){
            $sql .= " AND OT.STATUS='RC' AND
                RA.RECOMMEND_BY=".$id;
        }else if($status=='AP'){
            $sql .= " AND OT.STATUS='AP' AND
                RA.APPROVED_BY=".$id;
        }else if($status=='R'){
            $sql .=" AND OT.STATUS='".$status."' AND
                ((RA.RECOMMEND_BY=".$id." AND OT.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=".$id." AND OT.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY OT.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
}