<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

class PositionRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway('HR_POSITIONS',$adapter);		
	}
	public function add(Model $model){
		$this->tableGateway->insert($model->getArrayCopyForDB());
	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDB();
		unset($array["POSITION_ID"]);
		unset($array["CREATED_DT"]);
		$this->tableGateway->update($array,["POSITION_ID"=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete(["POSITION_ID"=>$id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select(['STATUS'=>'E']);       
    }
	public function fetchById($id){
		$row = $this->tableGateway->select(["POSITION_ID"=>$id]);
		return $row->current();
	}
}