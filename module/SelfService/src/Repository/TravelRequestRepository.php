<?php

namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TravelRequest;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
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
        EntityHelper::rawQueryResult($this->adapter, "
                DECLARE
                  V_FROM_DATE HRIS_EMPLOYEE_TRAVEL_REQUEST.FROM_DATE%TYPE;
                  V_EMPLOYEE_ID HRIS_EMPLOYEE_TRAVEL_REQUEST.EMPLOYEE_ID%TYPE;
                  V_STATUS HRIS_EMPLOYEE_TRAVEL_REQUEST.STATUS%TYPE;
                  V_TRAVEL_ID HRIS_EMPLOYEE_TRAVEL_REQUEST.TRAVEL_ID%TYPE:= {$id};
                BEGIN
                  SELECT FROM_DATE ,
                    EMPLOYEE_ID,
                    STATUS
                  INTO V_FROM_DATE,
                    V_EMPLOYEE_ID,
                    V_STATUS
                  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
                  WHERE TRAVEL_ID =V_TRAVEL_ID;
                  --
                  IF V_STATUS IN ('AP','C') AND V_FROM_DATE < TRUNC(SYSDATE) THEN
                    HRIS_REATTENDANCE(V_FROM_DATE,V_EMPLOYEE_ID);
                  END IF;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  DBMS_OUTPUT.PUT('NO DATA FOUND FOR ID =>'|| V_TRAVEL_ID);
                END;
");
    }

    public function updateDates($departureDate, $returnedDate, $requestedAmt, $travelId) {
        $this->tableGateway->update([
            TravelRequest::DEPARTURE_DATE => Helper::getExpressionDate($departureDate),
            TravelRequest::RETURNED_DATE => Helper::getExpressionDate($returnedDate),
            TravelRequest::REQUESTED_AMOUNT => $requestedAmt
                ], [TravelRequest::TRAVEL_ID => $travelId]);
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
            new Expression("TR.TRANSPORT_TYPE AS TRANSPORT_TYPE"),
            new Expression("TR.REQUESTED_TYPE AS REQUESTED_TYPE"),
            new Expression("INITCAP(TO_CHAR(TR.DEPARTURE_DATE, 'DD-MON-YYYY')) AS DEPARTURE_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RETURNED_DATE, 'DD-MON-YYYY')) AS RETURNED_DATE")
                ], true);

        $select->from(['TR' => TravelRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=TR." . TravelRequest::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left")
                ->join(['TS' => "HRIS_TRAVEL_SUBSTITUTE"], "TS.TRAVEL_ID=TR.TRAVEL_ID", ['SUB_EMPLOYEE_ID' => 'EMPLOYEE_ID', 'SUB_APPROVED_DATE' => new Expression("INITCAP(TO_CHAR(TS.APPROVED_DATE, 'DD-MON-YYYY'))"), 'SUB_REMARKS' => "REMARKS", 'SUB_APPROVED_FLAG' => "APPROVED_FLAG"], "left");

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
            new Expression("INITCAP(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("BS_DATE(TO_CHAR(TR.FROM_DATE, 'DD-MON-YYYY')) AS FROM_DATE_N"),
            new Expression("INITCAP(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("BS_DATE(TO_CHAR(TR.TO_DATE, 'DD-MON-YYYY')) AS TO_DATE_N"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("TR.DESTINATION AS DESTINATION"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_N"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
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
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=TR." . TravelRequest::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left");

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
