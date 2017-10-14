<?php

namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\AdvanceRequest;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AdvanceApproveRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AdvanceRequest::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function getAllWidStatus($id, $status) {
        
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [AdvanceRequest::ADVANCE_REQUEST_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY')) AS ADVANCE_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("INITCAP(TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
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
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=AR.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=AR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=AR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where([
            "AR.ADVANCE_REQUEST_ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllRequest($id = null) {
        $sql = "SELECT 
                    AR.ADVANCE_REQUEST_ID,
                    AR.EMPLOYEE_ID,
                    AR.REQUESTED_AMOUNT,
                    INITCAP(TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    BS_DATE(TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_N,
                    AR.APPROVED_BY,
                    AR.RECOMMENDED_BY,
                    AR.REASON,
                    AR.ADVANCE_ID,
                    AR.TERMS,
                    AR.RECOMMENDED_REMARKS,
                    AR.APPROVED_REMARKS,
                    INITCAP(TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY')) AS ADVANCE_DATE,
                    BS_DATE(TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY')) AS ADVANCE_DATE_N,
                    INITCAP(TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    INITCAP(E.FULL_NAME) AS FULL_NAME,
                    INITCAP(A.ADVANCE_NAME) AS ADVANCE_NAME,
                    A.ADVANCE_CODE,
                    A.ADVANCE_ID,
                    RA.RECOMMEND_BY as RECOMMENDER,
                    RA.APPROVED_BY AS APPROVER,
                    LEAVE_STATUS_DESC(AR.STATUS)                     AS STATUS,
                    REC_APP_ROLE({$id},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                    REC_APP_ROLE_NAME({$id},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE
                    FROM HRIS_EMPLOYEE_ADVANCE_REQUEST AR
                    LEFT JOIN HRIS_EMPLOYEES E ON 
                    E.EMPLOYEE_ID=AR.EMPLOYEE_ID
                    LEFT JOIN HRIS_ADVANCE_MASTER_SETUP A
                    ON AR.ADVANCE_ID=A.ADVANCE_ID
                    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                    ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                    WHERE A.STATUS = 'E' AND E.STATUS='E'
                    AND E.RETIRED_FLAG='N' 
                    AND ((RA.RECOMMEND_BY= {$id} AND AR.STATUS='RQ') OR (RA.APPROVED_BY= {$id} AND AR.STATUS='RC') )
                    ORDER BY AR.REQUESTED_DATE DESC"
        ;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
