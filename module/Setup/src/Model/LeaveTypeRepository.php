<?php

namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class LeaveTypeRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){

		$this->tableGateway = new TableGateway('leaveType',$adapter);
		
	}
	public function add(ModelInterface $model){
		 $this->tableGateway->insert($model->getArrayCopy());

	}
	public function edit(ModelInterface $model,$id){
		$this->tableGateway->update($model->getArrayCopy(),["leaveCode"=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete(["leaveCode"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchById($id){
		$row = $this->tableGateway->select(["leaveCode"=>$id]);
		return $row->current();
	}
}