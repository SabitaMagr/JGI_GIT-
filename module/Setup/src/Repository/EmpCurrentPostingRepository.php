<?php

namespace Setup\Repository;

use Setup\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class EmpCurrentPostingRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway('HR_EMPLOYEE_CURRENT_POSTING',$adapter);		
	}
	public function add(Model $model){
		$this->tableGateway->insert($model->getArrayCopyForDb());
	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDb();
		unset($array['EMPLOYEE_ID']);
		$this->tableGateway->update($array,["EMPLOYEE_ID"=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete(["EMPLOYEE_ID"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchById($id){
		$row = $this->tableGateway->select(["EMPLOYEE_ID"=>$id]);
		return $row->current();
	}
}