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
use Application\Helper\EntityHelper;

class AdvanceRequestRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AdvanceRequest::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([AdvanceRequest::STATUS => 'C'], [AdvanceRequest::ADVANCE_REQUEST_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY')) AS ADVANCE_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("INITCAP(TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("AR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("AR.REASON AS REASON"),
            new Expression("AR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("AR.APPROVED_BY AS APPROVED_BY"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("AR.ADVANCE_ID AS ADVANCE_ID"),
            new Expression("AR.TERMS AS TERMS")
                ], true);

        $select->from(['AR' => AdvanceRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=AR." . AdvanceRequest::EMPLOYEE_ID, ["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['A' => Advance::TABLE_NAME], "A." . Advance::ADVANCE_ID . "=AR." . AdvanceRequest::ADVANCE_ID, [Advance::ADVANCE_CODE, "ADVANCE_NAME"=>new Expression("INITCAP(A.ADVANCE_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.RECOMMENDED_BY", ['FN1' =>new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' =>new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' =>new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=AR.APPROVED_BY", ['FN2' =>new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' =>new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' =>new Expression("INITCAP(E2.LAST_NAME)")], "left");

        $select->where([
            "AR.ADVANCE_REQUEST_ID=" . $id
        ]);
        $select->order("AR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllByEmployeeId($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(AR.ADVANCE_DATE, 'DD-MON-YYYY')) AS ADVANCE_DATE"),
            new Expression("AR.STATUS AS STATUS"),
            new Expression("INITCAP(TO_CHAR(AR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(AR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("AR.ADVANCE_REQUEST_ID AS ADVANCE_REQUEST_ID"),
            new Expression("AR.REASON AS REASON"),
            new Expression("AR.ADVANCE_ID AS ADVANCE_ID"),
            new Expression("AR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("AR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("AR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("AR.TERMS AS TERMS")
                ], true);

        $select->from(['AR' => AdvanceRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=AR." . AdvanceRequest::EMPLOYEE_ID, ["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['A' => Advance::TABLE_NAME], "A." . Advance::ADVANCE_ID . "=AR." . AdvanceRequest::ADVANCE_ID, [Advance::ADVANCE_CODE, "ADVANCE_NAME"=>new Expression("INITCAP(A.ADVANCE_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=AR.RECOMMENDED_BY", ['FN1' =>new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' =>new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' =>new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=AR.APPROVED_BY", ['FN2' =>new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' =>new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' =>new Expression("INITCAP(E2.LAST_NAME)")], "left");

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

    public function checkAdvance(int $employeeId, int $monthId) {
        $sql = "SELECT COUNT(AM.MONTH_ID) AS MTH_CNT
                FROM
              (SELECT M.MONTH_ID
              FROM HRIS_MONTH_CODE M,
                (SELECT MC.FROM_DATE,
                  MC.TO_DATE,
                  R.TERMS
                FROM HRIS_MONTH_CODE MC,
                  (SELECT REQUESTED_DATE,
                    TERMS
                  FROM HRIS_EMPLOYEE_ADVANCE_REQUEST
                  WHERE STATUS    ='AP'
                  AND EMPLOYEE_ID =$employeeId
                  ) R
                WHERE R.REQUESTED_DATE BETWEEN MC.FROM_DATE AND MC.TO_DATE
                ) CM
              WHERE M.FROM_DATE >= CM.FROM_DATE
              AND ROWNUM        <=CM.TERMS
              ) AM
            WHERE AM.MONTH_ID= $monthId";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result['MTH_CNT'] > 0 ? 1 : 0;
    }

    public function getAdvance(int $employeeId, int $monthId) {
        $sql = "SELECT MTHS.*,MTHS.REQUESTED_AMOUNT/MTHS.TERMS AS SAL_CUT FROM (SELECT M.MONTH_ID,
CM.*
FROM HRIS_MONTH_CODE M,
  (SELECT MC.FROM_DATE,
    MC.TO_DATE,
    R.*
  FROM HRIS_MONTH_CODE MC,
    (SELECT ADVANCE_DATE,
      TERMS,
      ADVANCE_REQUEST_ID,
      REQUESTED_AMOUNT
    FROM HRIS_EMPLOYEE_ADVANCE_REQUEST
    WHERE STATUS    ='AP'
    AND EMPLOYEE_ID =$employeeId
    ) R
  WHERE R.ADVANCE_DATE BETWEEN MC.FROM_DATE AND MC.TO_DATE
  ) CM
WHERE M.FROM_DATE >= CM.FROM_DATE
AND ROWNUM        <=CM.TERMS) MTHS WHERE MTHS.MONTH_ID=$monthId";
        $statement = $this->adapter->query($sql);
        $rawResult = $statement->execute();
        $result = $rawResult->current();
        return $result == null ? 0 : $result['SAL_CUT'];
    }

}
