<?php

namespace Application\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Model\Months;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class MonthRepository implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Months::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        $rowset = $this->gateway->select(function (Select $select) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), [
                        'fromDate',
                        'toDate',
                    ]), false);

//            $select->where([Months::MONTH_ID => $id]);
            $select->where([Months::STATUS => 'E']);
        });
        return $rowset;
    }

    public function fetchByMonthId(int $id) {
//        $rowset = $this->gateway->select(function (Select $select) use ($id) {
//            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), [
//                        'fromDate',
//                        'toDate',
//                    ]), false);
//
//            $select->where([Months::MONTH_ID => $id]);
//            $select->where([Months::STATUS => 'E']);
//        });
//        return $rowset->current();


        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(Months::TABLE_NAME);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Months(), [
                    'fromDate',
                    'toDate',
                ]), false);

        $select->where([Months::MONTH_ID => $id]);
        $select->where([Months::STATUS => 'E']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchById($id) {
//        return $this->gateway->select([Months::FISCAL_YEAR_ID => $id, Months::STATUS => 'E']);
        return $this->gateway->select(function(Select $select) use($id) {
                    $select->where([Months::FISCAL_YEAR_ID => $id]);
                    $select->where([Months::STATUS => 'E']);
                    $select->where(function(Where $where) {
                        $where->lessThan(Months::TO_DATE, Helper::getcurrentExpressionDate());
                    });
                    $select->order(Months::FROM_DATE . " " . Select::ORDER_ASCENDING);
                });
    }

}
