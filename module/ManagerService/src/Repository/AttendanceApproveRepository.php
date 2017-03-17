<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/6/16
 * Time: 4:34 PM
 */
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\AttendanceRequestModel;
use AttendanceManagement\Model\AttendanceDetail;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class AttendanceApproveRepository implements RepositoryInterface
{
    private $adapter;
    private $tableGateway;
    private $tableGatewayAttendance;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(AttendanceRequestModel::TABLE_NAME, $adapter);
        $this->tableGatewayAttendance = new TableGateway(AttendanceDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        // TODO: Implement add() method.
    }

    public function getAllRequest($id = null,$status)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.REQUESTED_DT, 'DD-MON-YYYY') AS REQUESTED_DT"),
            new Expression("TO_CHAR(AR.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),
            new Expression("TO_CHAR(AR.APPROVED_DT, 'DD-MON-YYYY') AS APPROVED_DT"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("AR.APPROVED_BY AS APPROVED_BY"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("AR.ID AS ID"),
            new Expression("TO_CHAR(AR.IN_TIME, 'HH:MI AM') AS IN_TIME"),
            new Expression("TO_CHAR(AR.OUT_TIME, 'HH:MI AM') AS OUT_TIME"),
            new Expression("AR.IN_REMARKS AS IN_REMARKS"),
            new Expression("AR.OUT_REMARKS AS OUT_REMARKS"),
            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AR.TOTAL_HOUR AS TOTAL_HOUR"),
        ], true);

        $select->from(['AR' => AttendanceRequestModel::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=AR.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=AR.APPROVED_BY",['FIRST_NAME1'=>"FIRST_NAME",'MIDDLE_NAME1'=>"MIDDLE_NAME",'LAST_NAME1'=>"LAST_NAME"],"left")
            ->join(['RA'=>"HRIS_RECOMMENDER_APPROVER"],"RA.EMPLOYEE_ID=AR.EMPLOYEE_ID",['APPROVER'=>'RECOMMEND_BY'],"left")
            ->join(['APRV'=>"HRIS_EMPLOYEES"],"APRV.EMPLOYEE_ID=RA.RECOMMEND_BY",['APRV_FN'=>'FIRST_NAME','APRV_MN'=>'MIDDLE_NAME','APRV_LN'=>'LAST_NAME'],"left");

        $select->where([
            "AR.STATUS='".$status."'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N'",
            "RA.RECOMMEND_BY=".$id
        ]);
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function edit(Model $model, $id)
    {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[AttendanceRequestModel::ID=>$id]);
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"),new Expression("A.STATUS AS STATUS"),new Expression("A.IN_REMARKS AS IN_REMARKS"),new Expression("A.OUT_REMARKS AS OUT_REMARKS"),new Expression("A.TOTAL_HOUR AS TOTAL_HOUR"),new Expression("A.REQUESTED_DT AS REQUESTED_DT"),new Expression("A.APPROVED_REMARKS AS APPROVED_REMARKS")], true);
        $select->from(['A'=>AttendanceRequestModel::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ['FIRST_NAME','MIDDLE_NAME','LAST_NAME'],"left");
        $select->where([AttendanceRequestModel::ID=>$id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}