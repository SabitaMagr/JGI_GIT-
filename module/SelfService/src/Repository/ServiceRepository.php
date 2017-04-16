<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 12:13 PM
 */
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\JobHistory;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Helper\Helper;


class ServiceRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(JobHistory::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        // TODO: Implement add() method.
    }

    public function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }

    public function fetchAll()
    {

    }

//    public function fetchById($id)
//    {
//        $result = $this->tableGateway->select([JobHistory::JOB_HISTORY_ID=>$id]);
//        
//        echo '<pre>';
//        print_r($result->current());
//        echo '</pre>';
//        die();
//        return $result->current();
//    }
    
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['JH' => JobHistory::TABLE_NAME]);
        $select->where(["JH." . JobHistory::JOB_HISTORY_ID . "='".$id."'"]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new JobHistory(), [
                    'startDate',
                    'endDate'
                        ], NULL, 'JH'), false);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
//        echo '<pre>';
//        print_r($result->current());
//        die();
        return $result->current();
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
    public function getAllHistoryWidEmpId($employeeId,$fromDate,$toDate){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(H.START_DATE, 'DD-MON-YYYY') AS START_DATE"), new Expression("TO_CHAR(H.END_DATE, 'DD-MON-YYYY') AS END_DATE"), new Expression("H.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("H.JOB_HISTORY_ID AS JOB_HISTORY_ID")], true);
        $select->from(['H' => "HRIS_JOB_HISTORY"])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'H.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME'])
            ->join(['ST' => 'HRIS_SERVICE_EVENT_TYPES'], 'H.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID', ['SERVICE_EVENT_TYPE_NAME' => 'SERVICE_EVENT_TYPE_NAME'])
            ->join(['P1' => 'HRIS_POSITIONS'], 'P1.POSITION_ID=H.FROM_POSITION_ID', ['FROM_POSITION_NAME' => 'POSITION_NAME'])
            ->join(['P2' => 'HRIS_POSITIONS'], 'P2.POSITION_ID=H.TO_POSITION_ID', ['TO_POSITION_NAME' => 'POSITION_NAME'])
            ->join(['D1' => 'HRIS_DESIGNATIONS'], 'D1.DESIGNATION_ID=H.FROM_DESIGNATION_ID', ['FROM_DESIGNATION_TITLE' => 'DESIGNATION_TITLE'])
            ->join(['D2' => 'HRIS_DESIGNATIONS'], 'D2.DESIGNATION_ID=H.TO_DESIGNATION_ID', ['TO_DESIGNATION_TITLE' => 'DESIGNATION_TITLE'])
            ->join(['DES1' => 'HRIS_DEPARTMENTS'], 'DES1.DEPARTMENT_ID=H.FROM_DEPARTMENT_ID', ['FROM_DEPARTMENT_NAME' => 'DEPARTMENT_NAME'])
            ->join(['DES2' => 'HRIS_DEPARTMENTS'], 'DES2.DEPARTMENT_ID=H.TO_DEPARTMENT_ID', ['TO_DEPARTMENT_NAME' => 'DEPARTMENT_NAME'])
            ->join(['B1' => 'HRIS_BRANCHES'], 'B1.BRANCH_ID=H.FROM_BRANCH_ID', ['FROM_BRANCH_NAME' => 'BRANCH_NAME'])
            ->join(['B2' => 'HRIS_BRANCHES'], 'B2.BRANCH_ID=H.TO_BRANCH_ID', ['TO_BRANCH_NAME' => 'BRANCH_NAME'])
            ->join(['S1' => 'HRIS_SERVICE_TYPES'], 'S1.SERVICE_TYPE_ID=H.FROM_SERVICE_TYPE_ID', ['FROM_SERVICE_TYPE_NAME' => 'SERVICE_TYPE_NAME'])
            ->join(['S2' => 'HRIS_SERVICE_TYPES'], 'S2.SERVICE_TYPE_ID=H.TO_SERVICE_TYPE_ID', ['TO_SERVICE_TYPE_NAME' => 'SERVICE_TYPE_NAME']);
        
        if($fromDate!=null){
            $startDate = " AND H.START_DATE>=TO_DATE('".$fromDate."','DD-MM-YYYY')";
        }else{
            $startDate="";
        }
        if($toDate!=null){
            $endDate=" AND H.END_DATE<=TO_DATE('".$toDate."','DD-MM-YYYY')";
        }else{
            $endDate="";
        }
        $select->where([
            'H.EMPLOYEE_ID='.$employeeId.
            $startDate.$endDate
        ]);
        $select->order("H.START_DATE DESC");

        $statement = $sql->prepareStatementForSqlObject($select);
        //return $statement->getSql();
        $result = $statement->execute();
        return $result;
    }
}