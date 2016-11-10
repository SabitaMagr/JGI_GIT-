<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\JobHistory;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class JobHistoryRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(JobHistory::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [JobHistory::JOB_HISTORY_ID => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete([JobHistory::JOB_HISTORY_ID => $id]);
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(H.START_DATE, 'DD-MON-YYYY') AS START_DATE"), new Expression("TO_CHAR(H.END_DATE, 'DD-MON-YYYY') AS END_DATE"), new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID")], true);
        $select->from(['H' => "HR_JOB_HISTORY"])
            ->join(['E' => 'HR_EMPLOYEES'], 'H.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME'])
            ->join(['ST' => 'HR_SERVICE_EVENT_TYPES'], 'H.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID', ['SERVICE_EVENT_TYPE_NAME' => 'SERVICE_EVENT_TYPE_NAME'])
            ->join(['ST1' => 'HR_SERVICE_TYPES'], 'ST1.SERVICE_TYPE_ID=H.FROM_SERVICE_TYPE_ID', ['FROM_SERVICE_NAME' => 'SERVICE_TYPE_NAME'])
            ->join(['ST2' => 'HR_SERVICE_TYPES'], 'ST2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_NAME' => 'SERVICE_TYPE_NAME'])
            ->join(['P1' => 'HR_POSITIONS'], 'P1.POSITION_ID=H.FROM_POSITION_ID', ['FROM_POSITION_NAME' => 'POSITION_NAME'])
            ->join(['P2' => 'HR_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => 'POSITION_NAME'])
            ->join(['D1' => 'HR_DESIGNATIONS'], 'D1.DESIGNATION_ID=H.FROM_DESIGNATION_ID', ['FROM_DESIGNATION_TITLE' => 'DESIGNATION_TITLE'])
            ->join(['D2' => 'HR_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => 'DESIGNATION_TITLE'])
            ->join(['DES1' => 'HR_DEPARTMENTS'], 'DES1.DEPARTMENT_ID=H.FROM_DEPARTMENT_ID', ['FROM_DEPARTMENT_NAME' => 'DEPARTMENT_NAME'])
            ->join(['DES2' => 'HR_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' => 'DEPARTMENT_NAME'])
            ->join(['B1' => 'HR_BRANCHES'], 'B1.BRANCH_ID=H.FROM_BRANCH_ID', ['FROM_BRANCH_NAME' => 'BRANCH_NAME'])
            ->join(['B2' => 'HR_BRANCHES'], 'B2.BRANCH_ID=H.TO_BRANCH_ID', ['TO_BRANCH_NAME' => 'BRANCH_NAME']);

        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); die();
        $result = $statement->execute();
//        print '<pre>';
//        print_r($result->current());
//        exit;

//        $result= $this->tableGateway->select(function(Select $select){
//            $select->columns(Helper::convertColumnDateFormat($this->adapter, new JobHistory(), ['startDate','endDate']),false);
//        });
//        return    Helper::hydrate(JobHistory::class,$result);
        return $result;
    }

    public function fetchById($id)
    {
        $row = $this->tableGateway->select([JobHistory::JOB_HISTORY_ID => $id]);
        return $row->current();
    }
}