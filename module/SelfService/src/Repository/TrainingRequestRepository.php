<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\TrainingRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Setup\Model\Training;

class TrainingRequestRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TrainingRequest::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $currentDate = \Application\Helper\Helper::getcurrentExpressionDate();
        $this->tableGateway->update([TrainingRequest::STATUS => 'C', TrainingRequest::MODIFIED_DATE=>$currentDate], [TrainingRequest::REQUEST_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
             new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID") ,
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
                ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=TR.". TrainingRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['T' => Training::TABLE_NAME], "T.". Training::TRAINING_ID."=TR.". TrainingRequest::TRAINING_ID, [Training::TRAINING_CODE, Training::TRAINING_NAME,"T_START_DATE" => new Expression("INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))"),"T_END_DATE" => new Expression("INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))"),"T_DURATION"=> Training::DURATION,"T_TRAINING_TYPE"=>Training::TRAINING_TYPE],"left")
                ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=TR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=TR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "TR.REQUEST_ID=" . $id
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID") ,
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
                ], true);

         $select->from(['TR' => TrainingRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=TR.". TrainingRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['T' => Training::TABLE_NAME], "T.". Training::TRAINING_ID."=TR.". TrainingRequest::TRAINING_ID, [Training::TRAINING_CODE, Training::TRAINING_NAME,"T_START_DATE" => new Expression("INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))"),"T_END_DATE" => new Expression("INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))"),"T_DURATION"=> Training::DURATION,"T_TRAINING_TYPE"=>Training::TRAINING_TYPE],"left")
                ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=TR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=TR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
}