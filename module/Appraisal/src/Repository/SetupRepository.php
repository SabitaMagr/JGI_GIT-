<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Appraisal\Model\Setup;
use Application\Model\Model;
use Appraisal\Model\Type;
use Appraisal\Model\Stage;

class SetupRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Setup::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Setup::STATUS=>'D'],[Setup::APPRAISAL_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Setup::APPRAISAL_ID]);
        unset($data[Setup::CREATED_DATE]);
        unset($data[Setup::STATUS]);
        $this->tableGateway->update($data,[Setup::APPRAISAL_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("S.APPRAISAL_ID AS APPRAISAL_ID"), 
            new Expression("S.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("S.APPRAISAL_EDESC AS APPRAISAL_EDESC"), 
            new Expression("S.APPRAISAL_NDESC AS APPRAISAL_NDESC"),
            new Expression("INITCAP(TO_CHAR(S.START_DATE,'DD-MON-YYYY')) AS START_DATE"), 
            new Expression("INITCAP(TO_CHAR(S.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
            new Expression("S.REMARKS AS REMARKS")
            ], true);
        $select->from(['S' => Setup::TABLE_NAME])
                ->join(['AT' => Type::TABLE_NAME], 'AT.'.Type::APPRAISAL_TYPE_ID.'=S.'.Setup::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], "left")
                ->join(['AST' => Stage::TABLE_NAME], 'AST.'.Stage::STAGE_ID.'=S.'.Setup::CURRENT_STAGE_ID, [Stage::STAGE_EDESC], "left");
        
        $select->where(["S.".Setup::STATUS."='E'"]);
        $select->order("S.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("S.APPRAISAL_ID AS APPRAISAL_ID"), 
            new Expression("S.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("S.APPRAISAL_EDESC AS APPRAISAL_EDESC"), 
            new Expression("S.APPRAISAL_NDESC AS APPRAISAL_NDESC"),
            new Expression("INITCAP(TO_CHAR(S.START_DATE,'DD-MON-YYYY')) AS START_DATE"), 
            new Expression("INITCAP(TO_CHAR(S.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
            new Expression("S.REMARKS AS REMARKS"),
            new Expression("S.APPRAISAL_TYPE_ID AS APPRAISAL_TYPE_ID"),
            new Expression("S.CURRENT_STAGE_ID AS CURRENT_STAGE_ID")
            ], true);
        $select->from(['S' => Setup::TABLE_NAME])
                ->join(['AT' => Type::TABLE_NAME], 'AT.'.Type::APPRAISAL_TYPE_ID.'=S.'.Setup::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], "left")
                ->join(['AST' => Stage::TABLE_NAME], 'AST.'.Stage::STAGE_ID.'=S.'.Setup::CURRENT_STAGE_ID, [Stage::STAGE_EDESC], "left");
        
        $select->where(["S.".Setup::STATUS."='E' AND ".Setup::APPRAISAL_ID."=". $id]);
        $select->order("S.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
