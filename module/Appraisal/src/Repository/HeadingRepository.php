<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Appraisal\Model\Heading;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class HeadingRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
       $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Heading::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Heading::STATUS=>'D'],[Heading::HEADING_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Heading::HEADING_ID]);
        unset($data[Heading::CREATED_DATE]);
        unset($data[Heading::STATUS]);
        $this->tableGateway->update($data,[Heading::HEADING_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("AH.HEADING_ID AS HEADING_ID"), 
            new Expression("AH.HEADING_CODE AS HEADING_CODE"),
            new Expression("AH.HEADING_EDESC AS HEADING_EDESC"), 
            new Expression("AH.HEADING_NDESC AS HEADING_NDESC"),
            new Expression("AH.PERCENTAGE AS PERCENTAGE"),
            new Expression("AH.REMARKS AS REMARKS")
            ], true);
        $select->from(['AH' => "HR_APPRAISAL_HEADING"])
                ->join(['AT' => 'HR_APPRAISAL_TYPE'], 'AT.APPRAISAL_TYPE_ID=AH.APPRAISAL_TYPE_ID', ["APPRAISAL_TYPE_EDESC"], "left");
        
        $select->where(["AH.STATUS='E'"]);
        $select->order("AH.HEADING_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Heading::HEADING_ID => $id, Heading::STATUS => 'E']);
        return $result = $rowset->current();
    }
    
    public function fetchByAppraisalTypeId($appraisalTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("AH.HEADING_ID AS HEADING_ID"), 
            new Expression("AH.HEADING_CODE AS HEADING_CODE"),
            new Expression("AH.HEADING_EDESC AS HEADING_EDESC"), 
            new Expression("AH.HEADING_NDESC AS HEADING_NDESC"),
            new Expression("AH.PERCENTAGE AS PERCENTAGE"),
            new Expression("AH.REMARKS AS REMARKS")
            ], true);
        $select->from(['AH' => "HR_APPRAISAL_HEADING"])
                ->join(['AT' => 'HR_APPRAISAL_TYPE'], 'AT.APPRAISAL_TYPE_ID=AH.APPRAISAL_TYPE_ID', ["APPRAISAL_TYPE_EDESC"], "left");
        
        $select->where(["AH.STATUS='E' AND AH.APPRAISAL_TYPE_ID=".$appraisalTypeId]);
        $select->order("AH.HEADING_ID");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function getActiveRecord(){
        $sql = "SELECT QUESTION_ID, QUESTION_EDESC, 
   TO_CHAR(NULL) HEADING_EDESC, HEADING_ID  FROM HR_APPRAISAL_QUESTION
   UNION
   SELECT (NULL) QUESTION_ID, TO_CHAR(NULL) QUESTION_EDESC , HEADING_EDESC ,HEADING_ID
   FROM HR_APPRAISAL_HEADING WHERE APPRAISAL_TYPE_ID=6";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
        
    }

}