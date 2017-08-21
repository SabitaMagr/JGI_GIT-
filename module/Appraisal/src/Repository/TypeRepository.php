<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Appraisal\Model\Type;
use Application\Helper\EntityHelper;

class TypeRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Type::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Type::STATUS=>'D'],[Type::APPRAISAL_TYPE_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Type::APPRAISAL_TYPE_ID]);
        unset($array[Type::CREATED_DATE]);
        unset($array[Type::STATUS]);
        $this->tableGateway->update($array, [Type::APPRAISAL_TYPE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Type::class,[Type::APPRAISAL_TYPE_EDESC,Type::APPRAISAL_TYPE_NDESC],null,null,null,null,"AT"),false);
        $select->from(['AT' => "HRIS_APPRAISAL_TYPE"]);
        $select->where(["AT.STATUS='E'"]);
        $select->order("AT.APPRAISAL_TYPE_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Type::class,[Type::APPRAISAL_TYPE_EDESC,Type::APPRAISAL_TYPE_NDESC]),false);
            $select->where([Type::APPRAISAL_TYPE_ID => $id, Type::STATUS => 'E']);
        });
        return $result = $rowset->current();
    }

}