<?php
namespace Training\Repository;

use Application\Repository\RepositoryInterface;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Training\Model\TrainingAssign;
use Zend\Db\Sql\Sql;
use Setup\Model\Institute;
use Setup\Model\Training;

class TrainingAssignRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingAssign::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }
    public function getDetailByEmployeeID($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->column([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
        ],true);
        $select->from(['TA'=> TrainingAssign::TABLE_NAME]);
        $select->join(['T'=> Training::TABLE_NAME],"T.".Training::TRAINING_ID."=TA.".TrainingAssign::TRAINING_ID,[Training::TRAINING_NAME,Training::INSTRUCTOR_NAME],"left")
               ->join(['I'=> Institute::TABLE_NAME],"I.". Institute::INSTITUTE_ID."=T.". Training::INSTITUTE_ID,[Institute::INSTITUTE_NAME],"left");
    
        $select->where([
            "TA.EMPLOYEE_ID=" . $employeeId AND
            "TA.STATUS='E'"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); DIE();
        $result = $statement->execute();
        return $result->current();
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

}