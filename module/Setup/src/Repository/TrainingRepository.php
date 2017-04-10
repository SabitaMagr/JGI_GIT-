<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Training;
use Setup\Model\Institute;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Training\Model\TrainingAssign;
use Application\Helper\Helper;

class TrainingRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(Training::TABLE_NAME,$adapter);
        $this->adapter =  $adapter;

    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $temp=$model->getArrayCopyForDB();
        $this->tableGateway->update($temp,[Training::TRAINING_ID=>$id]);
    }

    public function fetchAll()
    {
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(T.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.END_DATE, 'DD-MON-YYYY') AS END_DATE"), 
            new Expression("T.TRAINING_CODE AS TRAINING_CODE"), 
            new Expression("T.TRAINING_NAME AS TRAINING_NAME"),
            new Expression("T.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("T.DURATION AS DURATION"),
            new Expression("T.TRAINING_ID AS TRAINING_ID"),
            ], true);
        $select->from(['T'=>Training::TABLE_NAME]);
        $select->join(['I' => Institute::TABLE_NAME], "T." . Training::INSTITUTE_ID . "=I.".Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME], 'left');
        $select->where(["T.STATUS='E'"]);
        $select->order("T.".Training::TRAINING_NAME." ASC");        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $arrayList = [];
        foreach($result as $row){
            if($row['TRAINING_TYPE']=='CP'){
                $row['TRAINING_TYPE']='Company Personal';
            }else if($row['TRAINING_TYPE']=='CC'){
                $row['TRAINING_TYPE']='Company Contribution';
            }else{
                $row['TRAINING_TYPE']='';
            }
            array_push($arrayList, $row);
        }
        return $arrayList;
    }

    public function fetchById($id)
    {
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(T.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.END_DATE, 'DD-MON-YYYY') AS END_DATE"), 
            new Expression("T.TRAINING_CODE AS TRAINING_CODE"), 
            new Expression("T.TRAINING_NAME AS TRAINING_NAME"),
            new Expression("T.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("T.COMPANY_ID AS COMPANY_ID"),
            new Expression("T.DURATION AS DURATION"),
            new Expression("T.TRAINING_ID AS TRAINING_ID"),
            new Expression("T.INSTRUCTOR_NAME AS INSTRUCTOR_NAME"),
            new Expression("T.REMARKS AS REMARKS"),
            new Expression("T.INSTITUTE_ID AS INSTITUTE_ID")
            ], true);
        $select->from(['T'=>Training::TABLE_NAME]);
        $select->join(['I' => Institute::TABLE_NAME], "T." . Training::INSTITUTE_ID . "=I.".Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME], 'left');
        $select->where(["T.TRAINING_ID=".$id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    
    //select only those training list that were not exist in training assign table
    public function selectAll($employeeId){
        $today = Helper::getcurrentExpressionDate();
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(T.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.END_DATE, 'DD-MON-YYYY') AS END_DATE"), 
            new Expression("T.TRAINING_CODE AS TRAINING_CODE"), 
            new Expression("T.TRAINING_NAME AS TRAINING_NAME"),
            new Expression("T.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("T.COMPANY_ID AS COMPANY_ID"),
            new Expression("T.DURATION AS DURATION"),
            new Expression("T.TRAINING_ID AS TRAINING_ID"),
            new Expression("T.INSTRUCTOR_NAME AS INSTRUCTOR_NAME"),
            new Expression("T.REMARKS AS REMARKS"),
            new Expression("T.INSTITUTE_ID AS INSTITUTE_ID")
            ], true);
        $select->from(['T'=>Training::TABLE_NAME]);
        $select->join(['I' => Institute::TABLE_NAME], "T." . Training::INSTITUTE_ID . "=I.".Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME], 'left');
        
        $select->where([
            "T.STATUS='E'",
            "T.TRAINING_ID NOT IN (SELECT TRAINING_ID FROM HRIS_EMPLOYEE_TRAINING_ASSIGN WHERE EMPLOYEE_ID=$employeeId)"
//            "T.END_DATE<=".$today->getExpression()
        ]);
       
       $select->order("T.START_DATE DESC");
       $statement = $sql->prepareStatementForSqlObject($select);
//       print_r($statement->getSql()); die();
       $result = $statement->execute();
       return $result;
    }

    public function delete($id)
    {
    	$this->tableGateway->update([Training::STATUS=>'D'],[Training::TRAINING_ID=>$id]);
    }
}