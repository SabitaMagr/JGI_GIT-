<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Position;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class PositionRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Position::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Position::POSITION_ID]);
        unset($array[Position::CREATED_DT]);
        $this->tableGateway->update($array, [Position::POSITION_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Position::STATUS => 'D'], [Position::POSITION_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchActiveRecord() {
//        $rowset = $this->tableGateway->select(function(Select $select) {
//            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Position::class,[Position::POSITION_NAME]),false);
//            $select->where([Position::STATUS => 'E']);
//            $select->order(Position::POSITION_NAME . " ASC");
//        });
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Position::class, [Position::POSITION_NAME],NULL,NULL,NULL,NULL,'P',FALSE,FALSE), false);
        $select->from(['P' => Position::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C.".Company::COMPANY_ID."=P.".Position::COMPANY_ID, [Company::COMPANY_NAME => new Expression('INITCAP(C.COMPANY_NAME)')], 'left');
        $select->where(["P.".Position::STATUS."='E'"]);
        $select->order("P.".Position::POSITION_NAME . " ASC");
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        $result = [];
        $i = 1;
        foreach ($rowset as $row) {
            array_push($result, [
                'SN' => $i,
                'POSITION_ID' => $row['POSITION_ID'],
                'POSITION_NAME' => $row['POSITION_NAME'],
                'REMARKS' => $row['REMARKS'],
                'COMPANY_NAME' => $row['COMPANY_NAME']
            ]);
            $i += 1;
        }
        return $result;
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select(function(Select $select)use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Position::class, [Position::POSITION_NAME]), false);
            $select->where([Position::POSITION_ID => $id]);
        });     
        return $row->current();
    }

}
