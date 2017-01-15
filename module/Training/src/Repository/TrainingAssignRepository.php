<?php
namespace Training\Repository;

use Application\Repository\RepositoryInterface;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Training\Model\TrainingAssign;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\Institute;
use Setup\Model\Training;
use Setup\Model\HrEmployees;

class TrainingAssignRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingAssign::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
       $this->tableGateway->update([TrainingAssign::STATUS=>'D'],[TrainingAssign::EMPLOYEE_ID."=$id[0]",TrainingAssign::TRAINING_ID." =$id[1]"]); 
    }
    
    public function getDetailByEmployeeID($employeeId,$trainingId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("TO_CHAR(T.".Training::START_DATE.", 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.".Training::END_DATE.", 'DD-MON-YYYY') AS END_DATE") 
        ],true);
        $select->from(['TA'=> TrainingAssign::TABLE_NAME]);
        $select->join(['T'=> Training::TABLE_NAME],"T.".Training::TRAINING_ID."=TA.".TrainingAssign::TRAINING_ID,[Training::TRAINING_ID, Training::TRAINING_CODE,Training::DURATION,Training::TRAINING_NAME,Training::INSTRUCTOR_NAME,Training::REMARKS, Training::TRAINING_TYPE],"left")
               ->join(['I'=> Institute::TABLE_NAME],"I.". Institute::INSTITUTE_ID."=T.". Training::INSTITUTE_ID,[Institute::INSTITUTE_NAME,Institute::LOCATION, Institute::EMAIL, Institute::TELEPHONE],"left")
               ->join(['E'=> HrEmployees::TABLE_NAME],"E.". HrEmployees::EMPLOYEE_ID."=TA.". TrainingAssign::EMPLOYEE_ID,[HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],"left");

    
        $select->where([
            "TA.EMPLOYEE_ID=" . $employeeId,  
            "TA.TRAINING_ID=" . $trainingId,
            "TA.STATUS='E'"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllTrainingList($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("TO_CHAR(T.".Training::START_DATE.", 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.".Training::END_DATE.", 'DD-MON-YYYY') AS END_DATE") 
        ],true);
        $select->from(['TA'=> TrainingAssign::TABLE_NAME]);
        $select->join(['T'=> Training::TABLE_NAME],"T.".Training::TRAINING_ID."=TA.".TrainingAssign::TRAINING_ID,[Training::TRAINING_ID, Training::TRAINING_CODE,Training::DURATION,Training::TRAINING_NAME,Training::INSTRUCTOR_NAME,Training::REMARKS, Training::TRAINING_TYPE],"left")
               ->join(['I'=> Institute::TABLE_NAME],"I.". Institute::INSTITUTE_ID."=T.". Training::INSTITUTE_ID,[Institute::INSTITUTE_NAME,Institute::LOCATION, Institute::EMAIL, Institute::TELEPHONE],"left");
    
        $select->where([
            "TA.EMPLOYEE_ID=" . $employeeId,            
            "TA.STATUS='E'"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function getAllDetailByEmployeeID($employeeId,$trainingId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("TO_CHAR(T.".Training::START_DATE.", 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.".Training::END_DATE.", 'DD-MON-YYYY') AS END_DATE")     
        ],true);
        $select->from(['TA'=> TrainingAssign::TABLE_NAME]);
        $select->join(['T'=> Training::TABLE_NAME],"T.".Training::TRAINING_ID."=TA.".TrainingAssign::TRAINING_ID,[Training::TRAINING_ID, Training::TRAINING_CODE,Training::DURATION,Training::TRAINING_NAME,Training::INSTRUCTOR_NAME,Training::REMARKS, Training::TRAINING_TYPE],"left")
               ->join(['I'=> Institute::TABLE_NAME],"I.". Institute::INSTITUTE_ID."=T.". Training::INSTITUTE_ID,[Institute::INSTITUTE_NAME],"left");
    
        $select->where([
            "TA.EMPLOYEE_ID=" . $employeeId,
            "TA.TRAINING_ID=" . $trainingId
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function filterRecords($employeeId,$branchId,$departmentId,$designationId,$positionId,$serviceTypeId,$serviceEventTypeId,$trainingId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("TO_CHAR(T.".Training::START_DATE.", 'DD-MON-YYYY') AS START_DATE"),
            new Expression("TO_CHAR(T.".Training::END_DATE.", 'DD-MON-YYYY') AS END_DATE")           
        ],true);
        $select->from(['TA'=> TrainingAssign::TABLE_NAME]);
        $select->join(['T'=> Training::TABLE_NAME],"T.".Training::TRAINING_ID."=TA.".TrainingAssign::TRAINING_ID,[Training::TRAINING_ID, Training::DURATION,Training::TRAINING_NAME,Training::INSTRUCTOR_NAME,Training::REMARKS, Training::TRAINING_TYPE],"left")
               ->join(['I'=> Institute::TABLE_NAME],"I.". Institute::INSTITUTE_ID."=T.". Training::INSTITUTE_ID,[Institute::INSTITUTE_NAME,Institute::LOCATION],"left")
               ->join(['E'=> HrEmployees::TABLE_NAME],"E.". HrEmployees::EMPLOYEE_ID."=TA.". TrainingAssign::EMPLOYEE_ID,[HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],"left");
    
        $select->where([
            "TA.STATUS='E'"
        ]);
        if($trainingId!=-1){
            $select->where([
                "TA.TRAINING_ID=".$trainingId
            ]);
        }
        
        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $select->where(["E.RETIRED_FLAG='Y'"]);
        } else {
            $select->where(["E.RETIRED_FLAG='N'"]);
        }

        if ($employeeId != -1) {
            $select->where([
                "E.EMPLOYEE_ID=" . $employeeId
            ]);
        }
        if ($branchId != -1) {
            $select->where([
                "E.BRANCH_ID=" . $branchId
            ]);
        }
        if ($departmentId != -1) {
            $select->where([
                "E.DEPARTMENT_ID=" . $departmentId
            ]);
        }
        if ($designationId != -1) {
            $select->where([
                "E.DESIGNATION_ID=" . $designationId
            ]);
        }
        if ($positionId != -1) {
            $select->where([
                "E.POSITION_ID=" . $positionId
            ]);
        }
        if ($serviceTypeId != -1) {
            $select->where([
                "E.SERVICE_TYPE_ID=" . $serviceTypeId
            ]);
        }
        if ($serviceEventTypeId != -1) {
            $select->where([
                "E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[TrainingAssign::EMPLOYEE_ID."=$id[0]",TrainingAssign::TRAINING_ID." =$id[1]"]);

    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

}