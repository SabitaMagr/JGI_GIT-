<?php
namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;

class PositionRepository implements PositionRepositoryInterface{
	private $tableGateway;

	public function __construct(TableGateway $positionTableGateway){
		$this->tableGateway = $positionTableGateway;
	}
	public function addPosition(Position $position){
		$this->tableGateway->insert($position->getArrayCopy());
	}
	public function editPosition(Position $position,$id){
		$this->tableGateway->update($position->getArrayCopy(),["positionCode"=>$id]);
	}
	public function deletePosition($id){
		$this->tableGateway->delete(["positionCode"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchById($id){
		$row = $this->tableGateway->select(["positionCode"=>$id]);
		return $row->current();
	}
}