<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAttendanceModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ContractAttendanceRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(ContractAttendanceModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
//        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContract::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ContractAttendanceModel::class, NULL, [
                    ContractAttendanceModel::ATTENDANCE_DT,
                        ], [ContractAttendanceModel::IN_TIME, ContractAttendanceModel::OUT_TIME], NUll, NULL, 'CA', NULL, NUll, [ContractAttendanceModel::TOTAL_HOUR]
                ), false);

        $select->from(['CA' => ContractAttendanceModel::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "CA." . ContractAttendanceModel::EMPLOYEE_ID . "=E.EMPLOYEE_ID", ['FULL_NAME' => new Expression("INITCAP(E.FULL_NAME)")], 'left');



        $select->where(['CA.' . ContractAttendanceModel::CONTRACT_ID => $id]);
        $select->order("CA.ATTENDANCE_DT ASC");

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result;
    }

    public function updateImportAttendance(Model $model, $contractId, $employeeId, $attendanceDate) {
        $updateArray = [
            ContractAttendanceModel::CONTRACT_ID => $contractId,
            ContractAttendanceModel::EMPLOYEE_ID => $employeeId,
            ContractAttendanceModel::ATTENDANCE_DT => $attendanceDate
        ];

        $tempArray = $model->getArrayCopyForDB();
        if (!array_key_exists('IN_TIME', $tempArray)||!array_key_exists('OUT_TIME', $tempArray)||!array_key_exists('TOTAL_HOUR', $tempArray)) {
            $tempArray['IN_TIME'] = null;
            $tempArray['OUT_TIME'] = null;
            $tempArray['TOTAL_HOUR'] = null;
        }

        $this->gateway->update($tempArray, $updateArray);
    }

}
