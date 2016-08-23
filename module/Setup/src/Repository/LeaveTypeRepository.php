<?php

namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class LeaveTypeRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){

		$this->tableGateway = new TableGateway('HR_LEAVE_TYPES',$adapter);
		
	}
	public function add(Model $model){
		 $this->tableGateway->insert($model->getArrayCopyForDB());

	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDB();
		unset($array['LEAVE_ID']);
		unset($array['CREATED_DATE']);
		$this->tableGateway->update($array,["LEAVE_ID"=>$id]);
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