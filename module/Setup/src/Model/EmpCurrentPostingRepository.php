<?php

namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class EmpCurrentPostingRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway('HR_EMPLOYEE_CURRENT_POSTING',$adapter);		
	}
	public function add($model){
		//print_r($model->getArrayCopyForDb()); die();
		$this->tableGateway->insert($model->getArrayCopyForDb());
	}
	public function edit($model,$id,$modifiedDt=null){
		$array = $model->getArrayCopyForDb();
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