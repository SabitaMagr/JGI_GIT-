<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\WorkOnDayoff;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;

class WorkOnDayoffRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(WorkOnDayoff::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $currentDate = \Application\Helper\Helper::getcurrentExpressionDate();
        $this->tableGateway->update([WorkOnDayoff::STATUS => 'C', WorkOnDayoff::MODIFIED_DATE=>$currentDate], [WorkOnDayoff::ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WD.ID AS ID"),
            new Expression("WD.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TO_CHAR(WD.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(WD.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE"),
            new Expression("TO_CHAR(WD.TO_DATE, 'DD-MON-YYYY') AS TO_DATE"),
            new Expression("WD.DURATION AS DURATION"),
            new Expression("WD.REMARKS AS REMARKS"),
            new Expression("WD.STATUS AS STATUS"),
            new Expression("WD.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TO_CHAR(WD.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("WD.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WD.APPROVED_BY AS APPROVED_BY"),
            new Expression("TO_CHAR(WD.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("WD.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TO_CHAR(WD.MODIFIED_DATE, 'DD-MON-YYYY') AS MODIFIED_DATE"), 
                ], true);

        $select->from(['WD' => WorkOnDayoff::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=WD.". WorkOnDayoff::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=WD.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=WD.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "WD.ID=" . $id
        ]);
        $select->order("WD.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WD.ID AS ID"),
            new Expression("WD.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TO_CHAR(WD.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(WD.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE"),
            new Expression("TO_CHAR(WD.TO_DATE, 'DD-MON-YYYY') AS TO_DATE"),
            new Expression("WD.DURATION AS DURATION"),
            new Expression("WD.REMARKS AS REMARKS"),
            new Expression("WD.STATUS AS STATUS"),
            new Expression("WD.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TO_CHAR(WD.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("WD.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WD.APPROVED_BY AS APPROVED_BY"),
            new Expression("TO_CHAR(WD.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("WD.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TO_CHAR(WD.MODIFIED_DATE, 'DD-MON-YYYY') AS MODIFIED_DATE"), 
                ], true);

        $select->from(['WD' => WorkOnDayoff::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=WD.". WorkOnDayoff::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=WD.RECOMMENDED_BY",['FN1'=>'FIRST_NAME','MN1'=>'MIDDLE_NAME','LN1'=>'LAST_NAME'],"left")
                ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=WD.APPROVED_BY",['FN2'=>'FIRST_NAME','MN2'=>'MIDDLE_NAME','LN2'=>'LAST_NAME'],"left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("WD.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}