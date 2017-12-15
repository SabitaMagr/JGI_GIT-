<?php

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Model\Months;
use Application\Repository\RepositoryInterface;
use Payroll\Model\SalarySheet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class SalarySheetRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(SalarySheet::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        return $this->gateway->delete([SalarySheet::MONTH_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        return $this->gateway->select(function (Select $select) {
                    $select->columns(Helper::convertColumnDateFormat($this->adapter, new SalarySheet(), [
                                'startDate',
                                'endDate',
                            ]), false);

                    $select->where([Months::STATUS => 'E']);
                });
    }

    public function fetchById($id) {
        return $this->gateway->select([SalarySheet::SHEET_NO => $id]);
    }

    public function fetchByIds(array $ids) {
        return $this->gateway->select($ids);
    }

    public function joinWithMonth($monthId = null, $employeeJoinDate = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([]);
        $select->from(['S' => SalarySheet::TABLE_NAME])
                ->join(['M' => Months::TABLE_NAME], 'S.' . SalarySheet::MONTH_ID . '=M.' . Months::MONTH_ID);
        if ($monthId != null) {
            $select->where([Months::MONTH_ID => $monthId]);
        }
        $select->where(["M." . Months::STATUS . " = " . "'E'"]);
        $select->where(["S." . SalarySheet::STATUS . " = " . "'E'"]);

        if ($employeeJoinDate != null) {
            $select->where(["'" . $employeeJoinDate . "'" . " <= " . "M." . Months::TO_DATE]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}
