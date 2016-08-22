<?php

namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class LeaveTypeRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){

		$this->tableGateway = new TableGateway('HR_LEAVE_TYPES',$adapter);
		
	}
	public function add(Model $model){
		 $this->tableGateway->insert($model->getArrayCopyForDb());

	}
	public function edit(Model $model,$id,$modifiedDt){
		$array = $model->getArrayCopyForDb();
		$newArray = array_merge($array,['MODIFIED_DT'=>$modifiedDt]);
		$this->tableGateway->update($newArray,["LEAVE_ID"=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete(["LEAVE_ID"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchById($id){
		$row = $this->tableGateway->select(["LEAVE_ID"=>$id]);
		return $row->current();
	}
}