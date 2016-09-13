<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\EmpCurrentPosting;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EmpCurrentPostingRepository implements RepositoryInterface{
	private $tableGateway;
private $adapter;
	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway(EmpCurrentPosting::TABLE_NAME,$adapter);
		$this->adapter=$adapter;
	}
	public function add(Model $model){
		$this->tableGateway->insert($model->getArrayCopyForDb());
	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDb();
		$this->tableGateway->update($array,[EmpCurrentPosting::EMPLOYEE_ID=>$id]);
	}
	public function delete($id){
		$this->tableGateway->delete([EmpCurrentPosting::EMPLOYEE_ID=>$id]);
	}
	public function fetchAll(){
//		return $this->tableGateway->select();

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from(['ECP' => "HR_EMPLOYEE_CURRENT_POSTING"])
			->join(['E' => 'HR_EMPLOYEES'], 'ECP.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME',"MIDDLE_NAME" => 'MIDDLE_NAME',"LAST_NAME" => 'LAST_NAME'])
			->join(['ST' => 'HR_SERVICE_TYPES'], 'ECP.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME' => 'SERVICE_TYPE_NAME'])
			->join(['P' => 'HR_POSITIONS'], 'P.POSITION_ID=ECP.POSITION_ID', ['POSITION_NAME' => 'POSITION_NAME'])
			->join(['D' => 'HR_DESIGNATIONS'], 'D.DESIGNATION_ID=ECP.DESIGNATION_ID', ['DESIGNATION_TITLE' => 'DESIGNATION_TITLE'])
			->join(['DEP' => 'HR_DEPARTMENTS'], 'DEP.DEPARTMENT_ID=ECP.DEPARTMENT_ID', ['DEPARTMENT_NAME' => 'DEPARTMENT_NAME'])
			->join(['B' => 'HR_BRANCHES'], 'B.BRANCH_ID=ECP.BRANCH_ID', ['BRANCH_NAME' => 'BRANCH_NAME']);


		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $statement->execute();

		return $result;
	}
	public function fetchById($id){
		$row = $this->tableGateway->select([EmpCurrentPosting::EMPLOYEE_ID=>$id]);
		return $row->current();
	}
}