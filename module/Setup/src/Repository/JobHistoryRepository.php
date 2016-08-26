<?php

namespace Setup\Repository;

use Application\Helper\Helper;
use Setup\Model\JobHistory;
use Setup\Model\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class JobHistoryRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway('HR_JOB_HISTORY', $adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['JOB_HISTORY_ID']);
        $this->tableGateway->update($array, ["JOB_HISTORY_ID" => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(["JOB_HISTORY_ID" => $id]);
    }

    public function fetchAll()
    {
        $result= $this->tableGateway->select(function(Select $select){
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new JobHistory(), ['startDate','endDate']),false);
        });
        $tempArray = [];
        foreach ($result as $item) {
            $tempObject = new JobHistory();
            $tempObject->exchangeArrayFromDB($item->getArrayCopy());

            array_push($tempArray, $tempObject);
        }
        return $tempArray;
    }

    public function fetchById($id)
    {
        $row = $this->tableGateway->select(["JOB_HISTORY_ID" => $id]);
        return $row->current();
    }
}