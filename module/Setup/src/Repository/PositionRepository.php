<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\Position;

class PositionRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway(Position::TABLE_NAME,$adapter);
	}
	public function add(Model $model){
		$this->tableGateway->insert($model->getArrayCopyForDB());
	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDB();
		unset($array[Position::POSITION_ID]);
		unset($array[Position::CREATED_DT]);
		$this->tableGateway->update($array,[Position::POSITION_ID=>$id]);
	}
	public function delete($id){
        $this->tableGateway->update([Position::STATUS=>'D'],[Position::POSITION_ID => $id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchActiveRecord()
    {
         return  $rowset= $this->tableGateway->select([Position::STATUS=>'E']);
    }
	public function fetchById($id){
		$row = $this->tableGateway->select([Position::POSITION_ID=>$id]);
		return $row->current();
	}
}