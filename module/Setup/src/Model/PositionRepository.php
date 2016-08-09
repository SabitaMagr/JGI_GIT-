<?php
namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class PositionRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){

		$this->tableGateway = new TableGateway('hr_positions',$adapter);
		
	}
	public function add(ModelInterface $model){
		 $this->tableGateway->insert($model->getArrayCopyForDb());

	}
	public function edit(ModelInterface $model,$id,$modifiedDt){
		$array = $model->getArrayCopyForDb();
		$newArray = array_merge($array,["MODIFIED_DT"=>$modifiedDt]);
		$this->tableGateway->update($newArray,["POSITION_ID"=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete(["POSITION_ID"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchById($id){
		$row = $this->tableGateway->select(["POSITION_ID"=>$id]);
		return $row->current();
	}
}