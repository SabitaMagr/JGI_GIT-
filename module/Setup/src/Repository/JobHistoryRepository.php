<?php

namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\JobHistory;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;

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
                ->join(['ST1' => 'HRIS_SERVICE_TYPES'], 'ST1.SERVICE_TYPE_ID=H.FROM_SERVICE_TYPE_ID', ['FROM_SERVICE_NAME' => new Expression("INITCAP(ST1.SERVICE_TYPE_NAME)")], "left")
                ->join(['ST2' => 'HRIS_SERVICE_TYPES'], 'ST2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_NAME' => new Expression("INITCAP(ST2.SERVICE_TYPE_NAME)")], "left")
                ->join(['P1' => 'HRIS_POSITIONS'], 'P1.POSITION_ID=H.FROM_POSITION_ID', ['FROM_POSITION_NAME' => new Expression("INITCAP(P1.POSITION_NAME)")], "left")
                ->join(['P2' => 'HRIS_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => new Expression("INITCAP(P2.POSITION_NAME)")], "left")
                ->join(['D1' => 'HRIS_DESIGNATIONS'], 'D1.DESIGNATION_ID=H.FROM_DESIGNATION_ID', ['FROM_DESIGNATION_TITLE' => new Expression("INITCAP(D1.DESIGNATION_TITLE)")], "left")
                ->join(['D2' => 'HRIS_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => new Expression("INITCAP(D2.DESIGNATION_TITLE)")], "left")
                ->join(['DES1' => 'HRIS_DEPARTMENTS'], 'DES1.DEPARTMENT_ID=H.FROM_DEPARTMENT_ID', ['FROM_DEPARTMENT_NAME' => new Expression("INITCAP(DES1.DEPARTMENT_NAME)")], "left")
                ->join(['DES2' => 'HRIS_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' => new Expression("INITCAP(DES2.DEPARTMENT_NAME)")], "left")
                ->join(['B1' => 'HRIS_BRANCHES'], 'B1.BRANCH_ID=H.FROM_BRANCH_ID', ['FROM_BRANCH_NAME' => new Expression("INITCAP(B1.BRANCH_NAME)")], "left")
                ->join(['B2' => 'HRIS_BRANCHES'], 'B2.BRANCH_ID=H.TO_BRANCH_ID', ['TO_BRANCH_NAME' => new Expression("INITCAP(B2.BRANCH_NAME)")], "left");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filter($fromDate, $toDate, $employeeId, $serviceEventTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(H.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(H.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID")], true);
        $select->from(['H' => "HRIS_JOB_HISTORY"])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'H.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left")
                ->join(['ST' => 'HRIS_SERVICE_EVENT_TYPES'], 'H.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID', ['SERVICE_EVENT_TYPE_NAME' => new Expression("INITCAP(ST.SERVICE_EVENT_TYPE_NAME)")], "left")
                ->join(['ST1' => 'HRIS_SERVICE_TYPES'], 'ST1.SERVICE_TYPE_ID=H.FROM_SERVICE_TYPE_ID', ['FROM_SERVICE_NAME' => new Expression("INITCAP(ST1.SERVICE_TYPE_NAME)")], "left")
                ->join(['ST2' => 'HRIS_SERVICE_TYPES'], 'ST2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_NAME' => new Expression("INITCAP(ST2.SERVICE_TYPE_NAME)")], "left")
                ->join(['P1' => 'HRIS_POSITIONS'], 'P1.POSITION_ID=H.FROM_POSITION_ID', ['FROM_POSITION_NAME' => new Expression("INITCAP(P1.POSITION_NAME)")], "left")
                ->join(['P2' => 'HRIS_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => new Expression("INITCAP(P2.POSITION_NAME)")], "left")
                ->join(['D1' => 'HRIS_DESIGNATIONS'], 'D1.DESIGNATION_ID=H.FROM_DESIGNATION_ID', ['FROM_DESIGNATION_TITLE' => new Expression("INITCAP(D1.DESIGNATION_TITLE)")], "left")
                ->join(['D2' => 'HRIS_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => new Expression("INITCAP(D2.DESIGNATION_TITLE)")], "left")
                ->join(['DES1' => 'HRIS_DEPARTMENTS'], 'DES1.DEPARTMENT_ID=H.FROM_DEPARTMENT_ID', ['FROM_DEPARTMENT_NAME' =>  new Expression("INITCAP(DES1.DEPARTMENT_NAME)")], "left")
                ->join(['DES2' => 'HRIS_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' =>  new Expression("INITCAP(DES2.DEPARTMENT_NAME)")], "left")
                ->join(['B1' => 'HRIS_BRANCHES'], 'B1.BRANCH_ID=H.FROM_BRANCH_ID', ['FROM_BRANCH_NAME' => new Expression("INITCAP(B1.BRANCH_NAME)")], "left")
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
                'H.EMPLOYEE_ID=' . $employeeId
            ]);
        }

        if ($serviceEventTypeId != -1) {
            $select->where([
                "H.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        $select->order("E.FIRST_NAME,H.START_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        //return $statement->getSql();
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
            new Expression("H.FROM_BRANCH_ID AS FROM_BRANCH_ID"),
            new Expression("H.TO_BRANCH_ID AS TO_BRANCH_ID"),
            new Expression("H.FROM_DEPARTMENT_ID AS FROM_DEPARTMENT_ID"),
            new Expression("H.TO_DEPARTMENT_ID AS TO_DEPARTMENT_ID"),
            new Expression("H.FROM_DESIGNATION_ID AS FROM_DESIGNATION_ID"),
            new Expression("H.TO_DESIGNATION_ID AS TO_DESIGNATION_ID"),
            new Expression("H.FROM_POSITION_ID AS FROM_POSITION_ID"),
            new Expression("H.TO_POSITION_ID AS TO_POSITION_ID"),
            new Expression("H.FROM_SERVICE_TYPE_ID AS FROM_SERVICE_TYPE_ID"),
            new Expression("H.TO_SERVICE_TYPE_ID AS TO_SERVICE_TYPE_ID"),
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
  )
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $result = Helper::extractDbData($result);
        return $result;
    }

}
