<?php
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\RepositoryInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use SelfService\Model\LeaveSubstitute;
use LeaveManagement\Model\LeaveApply;

class LeaveSubstituteRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveSubstitute::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->update($tempData,[LeaveSubstitute::LEAVE_REQUEST_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([LeaveSubstitute::LEAVE_REQUEST_ID=>$id]);
        return $result->current();
    }
    
    public function fetchByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_BS"),
            new Expression("INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.ID AS ID"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LA.APPROVED_BY AS APPROVED_BY")
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"),"FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME'=>new Expression("INITCAP(L.LEAVE_ENAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER' => 'RECOMMEND_BY', 'APPROVER' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECM_FN' =>  new Expression("INITCAP(RECM.FIRST_NAME)"), 'RECM_MN' => new Expression("INITCAP(RECM.MIDDLE_NAME)"), 'RECM_LN' => new Expression("INITCAP(RECM.LAST_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APRV_FN' =>  new Expression("INITCAP(APRV.FIRST_NAME)"), 'APRV_MN' => new Expression("INITCAP(APRV.MIDDLE_NAME)"), 'APRV_LN' => new Expression("INITCAP(APRV.LAST_NAME)")], "left")
                ->join(['LS'=>"HRIS_LEAVE_SUBSTITUTE"],"LS.LEAVE_REQUEST_ID=LA.ID",["SUB_EMPLOYEE_ID"=>"EMPLOYEE_ID","SUB_APPROVED_DATE"=>new Expression("INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))"),"SUB_APPROVED_FLAG"=>"APPROVED_FLAG"],"left");

        $select->where([
            "L.STATUS='E'",
            "LS.EMPLOYEE_ID=".$employeeId
        ]);
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}
