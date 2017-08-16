<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\JobHistory;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class JobHistoryRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(JobHistory::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [JobHistory::JOB_HISTORY_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->delete([JobHistory::JOB_HISTORY_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID")], true);
        $select->from(['H' => "HRIS_JOB_HISTORY"])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'H.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)")], "left")
                ->join(['ST' => 'HRIS_SERVICE_EVENT_TYPES'], 'H.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID', ['SERVICE_EVENT_TYPE_NAME' => new Expression("INITCAP(ST.SERVICE_EVENT_TYPE_NAME)")], "left")
                ->join(['ST2' => 'HRIS_SERVICE_TYPES'], 'ST2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_NAME' => new Expression("INITCAP(ST2.SERVICE_TYPE_NAME)")], "left")
                ->join(['P2' => 'HRIS_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => new Expression("INITCAP(P2.POSITION_NAME)")], "left")
                ->join(['D2' => 'HRIS_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => new Expression("INITCAP(D2.DESIGNATION_TITLE)")], "left")
                ->join(['DES2' => 'HRIS_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' => new Expression("INITCAP(DES2.DEPARTMENT_NAME)")], "left")
                ->join(['B2' => 'HRIS_BRANCHES'], 'B2.BRANCH_ID=H.TO_BRANCH_ID', ['TO_BRANCH_NAME' => new Expression("INITCAP(B2.BRANCH_NAME)")], "left");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filter($fromDate, $toDate, $employeeId, $serviceEventTypeId = null, $companyId = null, $branchId = null, $departmentId = null, $designationId = null, $positionId = null, $serviceTypeId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID")], true);
        $select->from(['H' => "HRIS_JOB_HISTORY"])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'H.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"), "FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['ST' => 'HRIS_SERVICE_EVENT_TYPES'], 'H.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID', ['SERVICE_EVENT_TYPE_NAME' => new Expression("INITCAP(ST.SERVICE_EVENT_TYPE_NAME)")], "left")
                ->join(['ST2' => 'HRIS_SERVICE_TYPES'], 'ST2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_NAME' => new Expression("INITCAP(ST2.SERVICE_TYPE_NAME)")], "left")
                ->join(['P2' => 'HRIS_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => new Expression("INITCAP(P2.POSITION_NAME)")], "left")
                ->join(['D2' => 'HRIS_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => new Expression("INITCAP(D2.DESIGNATION_TITLE)")], "left")
                ->join(['DES2' => 'HRIS_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' => new Expression("INITCAP(DES2.DEPARTMENT_NAME)")], "left")
                ->join(['B2' => 'HRIS_BRANCHES'], 'B2.BRANCH_ID=H.TO_BRANCH_ID', ['TO_BRANCH_NAME' => new Expression("INITCAP(B2.BRANCH_NAME)")], "left");

        if ($fromDate != null) {
            $select->where([
                "H.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')"
            ]);
        }

        if ($toDate != null) {
            $select->where([
                "H.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')"
            ]);
        }

        if ($employeeId != -1) {
            $select->where([
                'E.EMPLOYEE_ID=' . $employeeId
            ]);
        }

        if ($serviceEventTypeId != null && $serviceEventTypeId != -1) {
            $select->where([
                "H.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        if ($companyId != null && $companyId != -1) {
            $select->where([
                "E.COMPANY_ID=" . $companyId
            ]);
        }
        if ($branchId != null && $branchId != -1) {
            $select->where([
                "E.BRANCH_ID=" . $branchId
            ]);
        }
        if ($departmentId != null && $departmentId != -1) {
            $select->where([
                "E.DEPARTMENT_ID=" . $departmentId
            ]);
        }
        if ($designationId != null && $designationId != -1) {
            $select->where([
                "E.DESIGNATION_ID=" . $designationId
            ]);
        }
        if ($positionId != null && $positionId != -1) {
            $select->where([
                "E.POSITION_ID=" . $positionId
            ]);
        }
        if ($serviceTypeId != null && $serviceTypeId != -1) {
            $select->where([
                "E.SERVICE_TYPE_ID=" . $serviceTypeId
            ]);
        }
        $select->order("E.FIRST_NAME,H.START_DATE ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID"),
            new Expression("H.SERVICE_EVENT_TYPE_ID AS SERVICE_EVENT_TYPE_ID"),
            new Expression("H.TO_BRANCH_ID AS TO_BRANCH_ID"),
            new Expression("H.TO_DEPARTMENT_ID AS TO_DEPARTMENT_ID"),
            new Expression("H.TO_DESIGNATION_ID AS TO_DESIGNATION_ID"),
            new Expression("H.TO_POSITION_ID AS TO_POSITION_ID"),
            new Expression("H.TO_SERVICE_TYPE_ID AS TO_SERVICE_TYPE_ID"),
            new Expression("H.TO_COMPANY_ID AS TO_COMPANY_ID"),
            new Expression("H.TO_SALARY AS TO_SALARY"),
                ], true);
        $select->from(['H' => "HRIS_JOB_HISTORY"]);
        $select->where(['H.JOB_HISTORY_ID' => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchHistoryNotReview($empId) {
        $sql = "SELECT JH.JOB_HISTORY_ID,JH.START_DATE,SE.SERVICE_EVENT_TYPE_NAME
                FROM HRIS_JOB_HISTORY JH 
                JOIN HRIS_SERVICE_EVENT_TYPES SE
                ON (JH.SERVICE_EVENT_TYPE_ID=SE.SERVICE_EVENT_TYPE_ID)

                WHERE EMPLOYEE_ID=$empId
                AND JOB_HISTORY_ID NOT IN
                  (SELECT JOB_HISTORY_ID FROM HRIS_SALARY_DETAIL WHERE EMPLOYEE_ID=$empId
                  )";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $result = Helper::extractDbData($result);
        return $result;
    }

    function fetchLatestJobHistory($employeeId) {
        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, " 
                        SELECT TO_BRANCH_ID,
                          TO_DEPARTMENT_ID,
                          TO_DESIGNATION_ID,
                          TO_POSITION_ID,
                          TO_SERVICE_TYPE_ID,
                          TO_COMPANY_ID,
                          START_DATE_FORMATTED AS START_DATE
                        FROM
                          (SELECT TO_BRANCH_ID,
                            TO_DEPARTMENT_ID,
                            TO_DESIGNATION_ID,
                            TO_POSITION_ID,
                            TO_SERVICE_TYPE_ID,
                            TO_COMPANY_ID,
                            INITCAP(TO_CHAR(START_DATE,'DD-MON-YYYY')) AS START_DATE_FORMATTED
                          FROM HRIS_JOB_HISTORY
                          WHERE EMPLOYEE_ID ={$employeeId}
                          AND STATUS        ='E'
                          ORDER BY START_DATE DESC
                          )
                        WHERE ROWNUM=1"));
    }

    function fetchBeforeJobHistory($historyId) {
        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, "
                    SELECT INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
                      INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
                      H.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                      H.JOB_HISTORY_ID                                   AS JOB_HISTORY_ID,
                      H.SERVICE_EVENT_TYPE_ID                            AS SERVICE_EVENT_TYPE_ID,
                      H.TO_BRANCH_ID                                     AS TO_BRANCH_ID,
                      H.TO_DEPARTMENT_ID                                 AS TO_DEPARTMENT_ID,
                      H.TO_DESIGNATION_ID                                AS TO_DESIGNATION_ID,
                      H.TO_POSITION_ID                                   AS TO_POSITION_ID,
                      H.TO_SERVICE_TYPE_ID                               AS TO_SERVICE_TYPE_ID
                    FROM HRIS_JOB_HISTORY H,
                      (SELECT START_DATE FROM HRIS_JOB_HISTORY WHERE JOB_HISTORY_ID={$historyId}
                      ) PH
                    WHERE H.START_DATE<PH.START_DATE
                    AND ROWNUM        =1"));
    }

    function fetchAfterJobHistory($historyId) {

        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, "
                SELECT INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
                  INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
                  H.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                  H.JOB_HISTORY_ID                                   AS JOB_HISTORY_ID,
                  H.SERVICE_EVENT_TYPE_ID                            AS SERVICE_EVENT_TYPE_ID,
                  H.TO_BRANCH_ID                                     AS TO_BRANCH_ID,
                  H.TO_DEPARTMENT_ID                                 AS TO_DEPARTMENT_ID,
                  H.TO_DESIGNATION_ID                                AS TO_DESIGNATION_ID,
                  H.TO_POSITION_ID                                   AS TO_POSITION_ID,
                  H.TO_SERVICE_TYPE_ID                               AS TO_SERVICE_TYPE_ID
                FROM HRIS_JOB_HISTORY H,
                  (SELECT START_DATE FROM HRIS_JOB_HISTORY WHERE JOB_HISTORY_ID={$historyId}
                  ) PH
                WHERE H.START_DATE>PH.START_DATE
                AND ROWNUM        =1"));
    }

    function fetchAfterStartDate($date) {
        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, "
            SELECT INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
              INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
              H.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
              H.JOB_HISTORY_ID                                   AS JOB_HISTORY_ID,
              H.SERVICE_EVENT_TYPE_ID                            AS SERVICE_EVENT_TYPE_ID,
              H.TO_BRANCH_ID                                     AS TO_BRANCH_ID,
              H.TO_DEPARTMENT_ID                                 AS TO_DEPARTMENT_ID,
              H.TO_DESIGNATION_ID                                AS TO_DESIGNATION_ID,
              H.TO_POSITION_ID                                   AS TO_POSITION_ID,
              H.TO_SERVICE_TYPE_ID                               AS TO_SERVICE_TYPE_ID
            FROM HRIS_JOB_HISTORY H
            WHERE H.START_DATE>{$date}
            AND ROWNUM        =1"));
    }

    function displayAutoNotification() {
        EntityHelper::rawQueryResult($this->adapter, "");
    }

}
