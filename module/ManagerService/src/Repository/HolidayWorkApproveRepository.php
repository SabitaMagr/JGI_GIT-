<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\WorkOnHoliday;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class HolidayWorkApproveRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(WorkOnHoliday::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function getAllWidStatus($id, $status) {
        
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [WorkOnHoliday::ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WH.ID AS ID"),
            new Expression("WH.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("WH.HOLIDAY_ID AS HOLIDAY_ID"),
            new Expression("INITCAP(TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("WH.DURATION AS DURATION"),
            new Expression("WH.REMARKS AS REMARKS"),
            new Expression("WH.STATUS AS STATUS"),
            new Expression("WH.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("WH.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WH.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("WH.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
                ], true);

        $select->from(['WH' => WorkOnHoliday::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=WH.EMPLOYEE_ID", ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=WH.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=WH.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=WH.EMPLOYEE_ID", ['RECOMMENDER' => 'RECOMMEND_BY', 'APPROVER' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECM_FN' => new Expression("INITCAP(RECM.FIRST_NAME)"), 'RECM_MN' => new Expression("INITCAP(RECM.MIDDLE_NAME)"), 'RECM_LN' => new Expression("INITCAP(RECM.LAST_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APRV_FN' => new Expression("INITCAP(APRV.FIRST_NAME)"), 'APRV_MN' => new Expression("INITCAP(APRV.MIDDLE_NAME)"), 'APRV_LN' => new Expression("INITCAP(APRV.LAST_NAME)")], "left");

        $select->where([
            "WH.ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllRequest($id = null, $status = null) {
        $sql = "SELECT 
                    WH.ID,
                    WH.EMPLOYEE_ID,
                    INITCAP(TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE,
                    WH.APPROVED_BY,
                    WH.RECOMMENDED_BY,
                    WH.REMARKS,
                    WH.DURATION,
                    WH.STATUS,
                    WH.HOLIDAY_ID,
                    WH.RECOMMENDED_REMARKS,
                    WH.APPROVED_REMARKS,
                    INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE,
                    INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE,
                    INITCAP(TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE,
                    INITCAP(TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE,
                    INITCAP(TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE,
                    INITCAP(E.FIRST_NAME) AS FIRST_NAME,
                    INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME,
                    INITCAP(E.LAST_NAME) AS LAST_NAME,
                    INITCAP(E.FULL_NAME) AS FULL_NAME,
                    INITCAP(H.HOLIDAY_ENAME) AS HOLIDAY_ENAME,
                    INITCAP(H.HOLIDAY_LNAME) AS HOLIDAY_LNAME,
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
        if ($status == null) {
            $sql .= " AND ((RA.RECOMMEND_BY=" . $id . " AND WH.STATUS='RQ') OR (RA.APPROVED_BY=" . $id . " AND WH.STATUS='RC') )";
        } else if ($status == 'RC') {
            $sql .= " AND WH.STATUS='RC' AND
                RA.RECOMMEND_BY=" . $id;
        } else if ($status == 'AP') {
            $sql .= " AND WH.STATUS='AP' AND
                RA.APPROVED_BY=" . $id;
        } else if ($status == 'R') {
            $sql .= " AND WH.STATUS='" . $status . "' AND
                ((RA.RECOMMEND_BY=" . $id . " AND WH.APPROVED_DATE IS NULL) OR (RA.APPROVED_BY=" . $id . " AND WH.APPROVED_DATE IS NOT NULL) )";
        }
        $sql .= " ORDER BY WH.REQUESTED_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getWOHRuleType($employeeId) {
        return EntityHelper::rawQueryResult($this->adapter, "
                SELECT E.EMPLOYEE_ID,
                  P.WOH_FLAG
                FROM HRIS_EMPLOYEES E
                JOIN HRIS_POSITIONS P
                ON (E.POSITION_ID   = P.POSITION_ID)
                WHERE E.EMPLOYEE_ID ={$employeeId}")->current();
    }

    public function wohReward($wohId) {
        EntityHelper::rawQueryResult($this->adapter, "
                    BEGIN
                      HRIS_WOH_REWARD({$wohId});
                    END;");
    }

}
