<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\LeaveMaster;

class LeaveMasterRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LeaveMaster::TABLE_NAME,$adapter);
        $this->adapter=$adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }


    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['LEAVE_ID']);
        unset($array['CREATED_DT']);
        unset($array['STATUS']);
        $this->tableGateway->update($array,[LeaveMaster::LEAVE_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([LeaveMaster::STATUS=>'E']);
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//        $select->from("HR_LEAVE_MASTER_SETUP");
////        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Shift(), ['startTime','endTime']), false);
//        $select->where(['STATUS'=>'E']);
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//        return $result;
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([LeaveMaster::LEAVE_ID=>$id,LeaveMaster::STATUS=>'E']);
        return $rowset->current();
    }

    public function fetchActiveRecord()
    {
        return  $rowset= $this->tableGateway->select([LeaveMaster::STATUS=>'E']);
    }

    public function delete($id)
    {
//    	$this->tableGateway->delete(['SHIFT_ID'=>$id]);
        $this->tableGateway->update([LeaveMaster::STATUS=>'D'],[LeaveMaster::LEAVE_ID=>$id]);

    }
}