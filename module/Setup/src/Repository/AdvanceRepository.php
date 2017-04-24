<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Advance;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AdvanceRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Advance::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Advance::ADVANCE_ID]);
        unset($array[Advance::CREATED_DATE]);
        $this->tableGateway->update($array, [Advance::ADVANCE_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Advance::STATUS => 'D'], [Advance::ADVANCE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchActiveRecord() {
        $rowset = $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Advance::class, [Advance::ADVANCE_NAME]), false);
            $select->where([Advance::STATUS => 'E']);
            $select->order(Advance::ADVANCE_NAME . " ASC");
        });
        $result = [];
        $i = 1;
        foreach ($rowset as $row) {
            array_push($result, [
                'SN' => $i,
                'ADVANCE_ID' => $row['ADVANCE_ID'],
                'ADVANCE_CODE' => $row['ADVANCE_CODE'],
                'ADVANCE_NAME' => $row['ADVANCE_NAME'],
                'MIN_SALARY_AMT' => $row['MIN_SALARY_AMT'],
                'MAX_SALARY_AMT' => $row['MAX_SALARY_AMT'],
                'AMOUNT_TO_ALLOW' => $row['AMOUNT_TO_ALLOW'],
                'MONTH_TO_ALLOW' => $row['MONTH_TO_ALLOW'],
                'REMARKS' => $row['REMARKS']
            ]);
            $i += 1;
        }
        return $result;
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Advance::class, [Advance::ADVANCE_NAME]), false);
            $select->where([Advance::ADVANCE_ID => $id]);
        });
        return $row->current();
    }

}
