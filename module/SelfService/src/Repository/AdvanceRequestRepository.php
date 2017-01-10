<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\AdvanceRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Setup\Model\Advance;

class AdvanceRequestRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AdvanceRequest::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([AdvanceRequest::STATUS=>'C'],[AdvanceRequest::ADVANCE_REQUEST_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY') AS ADVANCE_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("AR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("AR.REASON AS REASON"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("AR.ADVANCE_ID AS ADVANCE_ID"),
            new Expression("AR.TERMS AS TERMS") 
                ], true);

        $select->from(['AR' => AdvanceRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=AR.". AdvanceRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['A' => Advance::TABLE_NAME], "A.".Advance::ADVANCE_ID."=AR.". AdvanceRequest::ADVANCE_ID, [Advance::ADVANCE_CODE, Advance::ADVANCE_NAME])
                ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=AR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=AR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "AR.ADVANCE_REQUEST_ID=" . $id
        ]);
        $select->order("AR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY') AS ADVANCE_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("AR.REASON AS REASON"),
            new Expression("AR.ADVANCE_ID AS ADVANCE_ID"),
            new Expression("AR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("AR.TERMS AS TERMS") 
                ], true);

        $select->from(['AR' => AdvanceRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=AR.". AdvanceRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['A' => Advance::TABLE_NAME], "A.". Advance::ADVANCE_ID."=AR.".AdvanceRequest::ADVANCE_ID, [Advance::ADVANCE_CODE, Advance::ADVANCE_NAME])
                ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=AR.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=AR.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("AR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
//        $list = [];
//        foreach($result  as $row){
//            array_push($list, $row);
//        }
        return $result;
    }

}