<?php

namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TravelRequest;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Zend\Db\TableGateway\TableGateway;

class TravelRequestRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TravelRequest::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([TravelRequest::STATUS => 'C'], [TravelRequest::TRAVEL_ID => $id]);
    }
    
    public function updateDates($departureDate,$returnedDate,$requestedAmt,$travelId){
        $this->tableGateway->update([
            TravelRequest::DEPARTURE_DATE=>Helper::getExpressionDate($departureDate),
            TravelRequest::RETURNED_DATE=>Helper::getExpressionDate($returnedDate),
            TravelRequest::REQUESTED_AMOUNT=>$requestedAmt
                ],[TravelRequest::TRAVEL_ID => $travelId]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchByReferenceId($id) {
        $result = $this->tableGateway->select([
            TravelRequest::REFERENCE_TRAVEL_ID . "=" . $id . " AND (STATUS!='C' AND STATUS!='R')"
        ]);
        return $result->current();
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.REFERENCE_TRAVEL_ID AS REFERENCE_TRAVEL_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE"),
            new Expression("INITCAP(TO_CHAR(TR.DEPARTURE_DATE, 'DD-MON-YYYY')) AS DEPARTURE_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RETURNED_DATE, 'DD-MON-YYYY')) AS RETURNED_DATE")
                ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=TR." . TravelRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['FN1' => 'FIRST_NAME', 'MN1' => 'MIDDLE_NAME', 'LN1' => 'LAST_NAME'], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.APPROVED_BY", ['FN2' => 'FIRST_NAME', 'MN2' => 'MIDDLE_NAME', 'LN2' => 'LAST_NAME'], "left");

        $select->where([
            "TR.TRAVEL_ID=" . $id
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllByEmployeeId($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY') AS FROM_DATE"),
            new Expression("TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY') AS TO_DATE"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY') AS APPROVED_DATE"),
            new Expression("TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY') AS RECOMMENDED_DATE"),
            new Expression("TR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("TR.TRAVEL_ID AS TRAVEL_ID"),
            new Expression("TR.TRAVEL_CODE AS TRAVEL_CODE"),
            new Expression("TR.PURPOSE AS PURPOSE"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE")
                ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=TR." . TravelRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['FN1' => 'FIRST_NAME', 'MN1' => 'MIDDLE_NAME', 'LN1' => 'LAST_NAME'], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.APPROVED_BY", ['FN2' => 'FIRST_NAME', 'MN2' => 'MIDDLE_NAME', 'LN2' => 'LAST_NAME'], "left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function checkEmployeeTravel(int $employeeId, Expression $date) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([TravelRequest::TRAVEL_ID], FALSE);
        $select->from(TravelRequest::TABLE_NAME);
        $select->where([TravelRequest::EMPLOYEE_ID => $employeeId]);
        $select->where([TravelRequest::STATUS => 'AP']);
        $select->where([$date->getExpression() . " BETWEEN " . TravelRequest::FROM_DATE . " AND " . TravelRequest::TO_DATE]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
