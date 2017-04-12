<?php
namespace LeaveManagement\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\LeaveMaster;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class LeaveMasterRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveMaster::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[LeaveMaster::LEAVE_ID]);
        unset($array[LeaveMaster::CREATED_DT]);
        unset($array[LeaveMaster::STATUS]);
        $this->tableGateway->update($array, [LeaveMaster::LEAVE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select){
            $select->where([LeaveMaster::STATUS => 'E']);
            $select->order(LeaveMaster::LEAVE_ENAME." ASC");
        });
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//        $select->from("HRIS_LEAVE_MASTER_SETUP");
////        $select->columns(Helper::convertColumnDateFormat($this->adapter, new Shift(), ['startTime','endTime']), false);
//        $select->where(['STATUS'=>'E']);
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([LeaveMaster::LEAVE_ID => $id, LeaveMaster::STATUS => 'E']);
        return $result = $rowset->current();
//        if($result!=null){
//            $r = $result->getArrayCopy();
//            print_r(($r));
//        }
//        //print_r($result->getArrayCopy());
//         die();
    }

    public function fetchActiveRecord() {
        return $rowset = $this->tableGateway->select(function(Select $select){
            $select->where([LeaveMaster::STATUS => 'E']);
            $select->order(LeaveMaster::LEAVE_ENAME." ASC");
        });
    }

    public function delete($id) {
//    	$this->tableGateway->delete(['SHIFT_ID'=>$id]);
        $this->tableGateway->update([LeaveMaster::STATUS => 'D'], [LeaveMaster::LEAVE_ID => $id]);
    }

    public function checkIfCashable(int $leaveId) {
        $leave = $this->tableGateway->select([LeaveMaster::LEAVE_ID => $leaveId, LeaveMaster::STATUS => 'E'])->current();
        return ($leave[LeaveMaster::CASHABLE] == 'Y') ? true : false;
    }
    
    public function getSubstituteLeave(){
        $result =  $this->tableGateway->select([LeaveMaster::STATUS => 'E', LeaveMaster::IS_SUBSTITUTE=>'Y']);
        return $result->current();
    }
}
