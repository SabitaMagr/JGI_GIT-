<?php

namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\TrainingRequest;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\Training;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;

class TrainingApproveRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TrainingRequest::TABLE_NAME, $adapter);
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function getAllWidStatus($id, $status) {
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [TrainingRequest::REQUEST_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("INITCAP(TR.TITLE) AS TITLE"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
                ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
                ->join(['T' => Training::TABLE_NAME], "T." . Training::TRAINING_ID . "=TR." . TrainingRequest::TRAINING_ID, [Training::TRAINING_CODE, "TRAINING_NAME" => new Expression("INITCAP(T.TRAINING_NAME)"), "T_START_DATE" => new Expression("INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))"), "T_END_DATE" => new Expression("INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))"), "T_DURATION" => Training::DURATION, "T_TRAINING_TYPE" => Training::TRAINING_TYPE], "left")
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=TR.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
        ;
        $select->where([
            "TR.REQUEST_ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllRequest($id = null, $status = null) {
        $sql = "
                SELECT TR.REQUEST_ID,
                  TR.EMPLOYEE_ID,
                  INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                  BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_N,
                  TR.APPROVED_BY,
                  TR.RECOMMENDED_BY,
                  TR.REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.DURATION
                    ELSE T.DURATION
                  END) AS DURATION ,
                  TR.DESCRIPTION,
                  INITCAP(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TITLE
                    ELSE T.TRAINING_NAME
                  END) AS TITLE,
                  TR.STATUS,
                  TR.TRAINING_ID,
                  TRAINING_TYPE_DESC(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TRAINING_TYPE
                    ELSE T.TRAINING_TYPE
                  END) AS TRAINING_TYPE,
                  TR.RECOMMENDED_REMARKS,
                  TR.APPROVED_REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))
                  END) AS START_DATE,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.START_DATE)
                    ELSE BS_DATE(T.START_DATE)
                  END) AS START_DATE_N,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))
                  END) AS END_DATE,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.END_DATE)
                    ELSE BS_DATE(T.END_DATE)
                  END)                                                    AS END_DATE_N,
                  INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY'))    AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY'))       AS APPROVED_DATE,
                  INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY'))       AS MODIFIED_DATE,
                  INITCAP(E.FIRST_NAME)                                   AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME)                                  AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)                                    AS LAST_NAME,
                  INITCAP(E.FULL_NAME)                                    AS FULL_NAME,
                  INITCAP(T.TRAINING_NAME)                                AS TRAINING_NAME,
                  RA.RECOMMEND_BY                                         AS RECOMMENDER,
                  RA.APPROVED_BY                                          AS APPROVER,
                  LEAVE_STATUS_DESC(TR.STATUS)                            AS STATUS ,
                  REC_APP_ROLE({$id},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME({$id},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                FROM HRIS_EMPLOYEE_TRAINING_REQUEST TR
                LEFT JOIN HRIS_TRAINING_MASTER_SETUP T
                ON T.TRAINING_ID=TR.TRAINING_ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON E.EMPLOYEE_ID  =RA.EMPLOYEE_ID
                WHERE E.STATUS    ='E'
                AND E.RETIRED_FLAG='N'";
        if ($status == null) {
            $sql .= " AND ((RA.RECOMMEND_BY=" . $id . " AND TR.STATUS='RQ') OR (RA.APPROVED_BY=" . $id . " AND TR.STATUS='RC') )";
        } else if ($status == 'RC') {
            $sql .= " AND TR.STATUS='RC' AND
                RA.RECOMMEND_BY=" . $id;
        } else if ($status == 'AP') {
            $sql .= " AND TR.STATUS='AP' AND
                RA.APPROVED_BY=" . $id;
        } else if ($status == 'R') {
            $sql .= " AND TR.STATUS='" . $status . "' AND
                ((RA.RECOMMEND_BY=" . $id . " AND TR.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=" . $id . " AND TR.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY TR.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
