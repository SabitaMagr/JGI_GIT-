<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\WorkOnHoliday;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use HolidayManagement\Model\Holiday;

class WorkOnHolidayRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(WorkOnHoliday::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $currentDate = \Application\Helper\Helper::getcurrentExpressionDate();
        $this->tableGateway->update([WorkOnHoliday::STATUS => 'C', WorkOnHoliday::MODIFIED_DATE=>$currentDate], [WorkOnHoliday::ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WH.ID AS ID"),
            new Expression("WH.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("WH.HOLIDAY_ID AS HOLIDAY_ID") ,
            new Expression("INITCAP(TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("WH.DURATION AS DURATION"),
            new Expression("WH.REMARKS AS REMARKS"),
            new Expression("WH.STATUS AS STATUS"),
            new Expression("WH.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("WH.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WH.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("WH.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
                ], true);

        $select->from(['WH' => WorkOnHoliday::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=WH.".WorkOnHoliday::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['H' => Holiday::TABLE_NAME], "H.". Holiday::HOLIDAY_ID."=WH.". WorkOnHoliday::HOLIDAY_ID, [Holiday::HOLIDAY_CODE, "HOLIDAY_ENAME"=> new Expression("INITCAP(H.HOLIDAY_ENAME)")])
                ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=WH.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=WH.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")],"left");

        $select->where([
            "WH.ID=" . $id
        ]);
        $select->order("WH.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("WH.ID AS ID"),
            new Expression("WH.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("WH.HOLIDAY_ID AS HOLIDAY_ID") ,
            new Expression("INITCAP(TO_CHAR(WH.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(WH.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("WH.DURATION AS DURATION"),
            new Expression("WH.REMARKS AS REMARKS"),
            new Expression("WH.STATUS AS STATUS"),
            new Expression("WH.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("WH.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("WH.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(WH.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("WH.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(WH.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"), 
                ], true);

        $select->from(['WH' => WorkOnHoliday::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=WH.".WorkOnHoliday::EMPLOYEE_ID,["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['H' => Holiday::TABLE_NAME], "H.". Holiday::HOLIDAY_ID."=WH.". WorkOnHoliday::HOLIDAY_ID, [Holiday::HOLIDAY_CODE, "HOLIDAY_ENAME"=> new Expression("INITCAP(H.HOLIDAY_ENAME)")])
                ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=WH.RECOMMENDED_BY",['FN1' =>  new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")],"left")
                ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=WH.APPROVED_BY",['FN2' =>  new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")],"left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("WH.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}