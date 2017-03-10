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
    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
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
            new Expression("AS.APPRAISAL_ID AS APPRAISAL_ID"), 
            new Expression("AS.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("AS.APPRAISAL_EDESC AS APPRAISAL_EDESC"), 
            new Expression("AS.APPRAISAL_NDESC AS APPRAISAL_NDESC"),
            new Expression("TO_CHAR(AS.START_DATE,'DD-MON-YYYY') AS START_DATE"), 
            new Expression("TO_CHAR(AS.END_DATE,'DD-MON-YYYY') AS END_DATE"),
            new Expression("AS.REMARKS AS REMARKS")
            ], true);
        $select->from(['AS' => Setup::TABLE_NAME])
                ->join(['AT' => Type::TABLE_NAME], 'AT.'.Type::APPRAISAL_TYPE_ID.'=AS.'.Setup::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], "left")
                ->join(['AST' => Stage::TABLE_NAME], 'AST.'.Stage::STAGE_ID.'=AS.'.Setup::CURRENT_STAGE_ID, [Stage::STAGE_EDESC], "left");
        
        $select->where(["AS.".Setup::STATUS."='E'"]);
        $select->order("AS.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Setup::APPRAISAL_ID => $id, Setup::STATUS => 'E']);
        return $result = $rowset->current();
    }

}
