<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/6/16
 * Time: 3:20 PM
 */
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\AttendanceRequestModel;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class AttendanceRequestRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(AttendanceRequestModel::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AttendanceRequestModel::ID => $id]);
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"),
            new Expression("INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM')) AS IN_TIME"),
            new Expression("INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM')) AS OUT_TIME"),
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), 
            new Expression("A.ID AS ID"),
            new Expression("A.IN_REMARKS AS IN_REMARKS"),
            new Expression("A.OUT_REMARKS AS OUT_REMARKS")
            ], true);
        $select->from(['A'=>AttendanceRequestModel::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")],"left");
        $select->order("A.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"),
            new Expression("INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM')) AS IN_TIME"),
            new Expression("INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM')) AS OUT_TIME"),
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("A.ID AS ID"),
            new Expression("A.IN_REMARKS AS IN_REMARKS"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPROVED_BY AS APPROVED_BY"),
            new Expression("A.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("A.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("A.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("INITCAP(TO_CHAR(A.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT"),
            new Expression("INITCAP(TO_CHAR(A.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("A.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR")], true);
        $select->from(['A'=>AttendanceRequestModel::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=A.APPROVED_BY",['FIRST_NAME1'=>new Expression("INITCAP(E1.FIRST_NAME)"),'MIDDLE_NAME1'=>new Expression("INITCAP(E1.MIDDLE_NAME)"),'LAST_NAME1'=>new Expression("INITCAP(E1.LAST_NAME)")],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=A.EMPLOYEE_ID",['RECOMMENDER'=>'RECOMMEND_BY','APPROVER'=>'APPROVED_BY'],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=A.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E2.LAST_NAME)")],"left")
            ->join(['E3'=>"HRIS_EMPLOYEES"],"E3.EMPLOYEE_ID=A.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E3.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E3.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E3.LAST_NAME)")],"left")
            ->join(['RECM'=>"HRIS_EMPLOYEES"],"RECM.EMPLOYEE_ID=RA.RECOMMEND_BY",['RECM_FN'=>new Expression("INITCAP(RECM.FIRST_NAME)"),'RECM_MN'=>new Expression("INITCAP(RECM.MIDDLE_NAME)"),'RECM_LN'=>new Expression("INITCAP(RECM.LAST_NAME)")],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.APPROVED_BY",['APRV_FN'=>new Expression("INITCAP(APRV.FIRST_NAME)"),'APRV_MN'=>new Expression("INITCAP(APRV.MIDDLE_NAME)"),'APRV_LN'=>new Expression("INITCAP(APRV.LAST_NAME)")],"left");

        $select->where([AttendanceRequestModel::ID=>$id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchByEmpId($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"), 
            new Expression("INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM')) AS IN_TIME"), 
            new Expression("INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM')) AS OUT_TIME"), 
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("A.ID AS ID"),
            new Expression("A.IN_REMARKS AS IN_REMARKS"), 
            new Expression("A.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR"),
            new Expression("A.STATUS AS STATUS")
            ], true);
        $select->from(['A' => AttendanceRequestModel::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" =>  new Expression("INITCAP(E.FIRST_NAME)")],"left");
        $select->where(['A.EMPLOYEE_ID'=> $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function delete($id)
    {
        $this->tableGateway->update([AttendanceRequestModel::STATUS=>'C'],[AttendanceRequestModel::ID=>$id]);
    }
    public function getFilterRecords($data){
        $employeeId = $data['employeeId'];
        $attendanceRequestStatusId = $data['attendanceRequestStatusId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(A.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT"),
            new Expression("INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT"), 
            new Expression("INITCAP(TO_CHAR(A.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM')) AS IN_TIME"), 
            new Expression("INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM')) AS OUT_TIME"), 
            new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), 
            new Expression("A.ID AS ID"), 
            new Expression("A.IN_REMARKS AS IN_REMARKS"), 
            new Expression("A.OUT_REMARKS AS OUT_REMARKS"), 
            new Expression("A.TOTAL_HOUR AS TOTAL_HOUR"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("A.APPROVED_BY AS APPROVED_BY")
            ], true);
        $select->from(['A' => AttendanceRequestModel::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)")],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=A.APPROVED_BY",['FN2'=>new Expression("INITCAP(E2.FIRST_NAME)"),'MN2'=>new Expression("INITCAP(E2.MIDDLE_NAME)"),'LN2'=>new Expression("INITCAP(E2.LAST_NAME)")],"left")
                ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=A.EMPLOYEE_ID",['APPROVER'=>'RECOMMEND_BY'],"left")
                ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.RECOMMEND_BY",['APRV_FN'=>new Expression("INITCAP(APRV.FIRST_NAME)"),'APRV_MN'=>new Expression("INITCAP(APRV.MIDDLE_NAME)"),'APRV_LN'=>new Expression("INITCAP(APRV.LAST_NAME)")],"left");

        $select->where(['A.EMPLOYEE_ID='.$employeeId]);
        
         if($attendanceRequestStatusId!=-1){
            $select->where([
                "A.STATUS='" . $attendanceRequestStatusId."'"
            ]);
        }
        
        if($fromDate!=null){
            $select->where([
                "A.ATTENDANCE_DT>=TO_DATE('".$fromDate."','DD-MM-YYYY')"
            ]);
        }
        if($toDate!=null){
            $select->where([
                "A.ATTENDANCE_DT<=TO_DATE('".$toDate."','DD-MM-YYYY')"
            ]);
        }
        $select->order("A.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;        
    }
}