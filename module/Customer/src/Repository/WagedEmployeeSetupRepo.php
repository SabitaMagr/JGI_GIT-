<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\WagedEmployeeSetupModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class WagedEmployeeSetupRepo implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(WagedEmployeeSetupModel::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([WagedEmployeeSetupModel::STATUS => EntityHelper::STATUS_DISABLED], [WagedEmployeeSetupModel::EMPLOYEE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [WagedEmployeeSetupModel::EMPLOYEE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $columns = EntityHelper::getColumnNameArrayWithOracleFns(WagedEmployeeSetupModel::class, [WagedEmployeeSetupModel::FIRST_NAME, WagedEmployeeSetupModel::MIDDLE_NAME, WagedEmployeeSetupModel::LAST_NAME, WagedEmployeeSetupModel::FULL_NAME], [
                    WagedEmployeeSetupModel::ID_CITIZENSHIP_ISSUE_DATE,
                        ], NULL, NULL, NULL, 'WE');
        $select->columns($columns, false);

        $select->from(['WE' => WagedEmployeeSetupModel::TABLE_NAME])
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "WE." . WagedEmployeeSetupModel::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left');

        $select->where([
            "WE.STATUS='E'"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);

//        echo $statement->getSql();
//        die();
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $columns = EntityHelper::getColumnNameArrayWithOracleFns(WagedEmployeeSetupModel::class, [WagedEmployeeSetupModel::FIRST_NAME, WagedEmployeeSetupModel::MIDDLE_NAME, WagedEmployeeSetupModel::LAST_NAME, WagedEmployeeSetupModel::FULL_NAME], [
                    WagedEmployeeSetupModel::ID_CITIZENSHIP_ISSUE_DATE,
                        ], NULL, NULL, NULL, 'WE');
        $select->columns($columns, false);

        $select->from(['WE' => WagedEmployeeSetupModel::TABLE_NAME])
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "WE." . WagedEmployeeSetupModel::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left');

        $select->where([
            "WE.STATUS='E'"
        ]);
        $select->where([
            "WE.EMPLOYEE_ID=$id"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
