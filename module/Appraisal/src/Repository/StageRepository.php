<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Appraisal\Model\Stage;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class StageRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
       $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Stage::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Stage::STATUS=>'D'],[Stage::STAGE_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($array[Stage::STAGE_ID]);
        unset($array[Stage::CREATED_DATE]);
        unset($array[Stage::STATUS]);
        $this->tableGateway->update($data,[Stage::STAGE_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(START_DATE,'DD-MON-YYYY') AS START_DATE"), 
            new Expression("TO_CHAR(END_DATE,'DD-MON-YYYY') AS END_DATE"),
            new Expression("STAGE_ID AS STAGE_ID"),
            new Expression("STAGE_CODE AS STAGE_CODE"),
            new Expression("STAGE_EDESC AS STAGE_EDESC"),
            new Expression("STAGE_NDESC AS STAGE_NDESC"),
            new Expression("ORDER_NO AS ORDER_NO")
            ], true);
        $select->from("HR_APPRAISAL_STAGE");
        
        $select->where(["STATUS='E'"]);
        $select->order("STAGE_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select([Stage::STAGE_ID=>$id]);
        return $result->current();
    }

}
