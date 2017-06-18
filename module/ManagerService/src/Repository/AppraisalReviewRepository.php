<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Appraisal\Model\Stage;
use Appraisal\Model\Type;
use Appraisal\Model\Setup;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\AppraisalAssign;

class AppraisalReviewRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalAnswer::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function getAllRequest($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("A.APPRAISAL_ID AS APPRAISAL_ID"),
            new Expression("A.APPRAISAL_TYPE_ID AS APPRAISAL_TYPE_ID"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("A.APPRAISAL_EDESC AS APPRAISAL_EDESC"),
            new Expression("A.REMARKS AS REMARKS"),
            new Expression("A.KPI_SETTING AS KPI_SETTING"),
            new Expression("A.COMPETENCIES_SETTING AS COMPETENCIES_SETTING"),
            new Expression("INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE"), 
            new Expression("INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
        ]);
        $select->from(["A"=>Setup::TABLE_NAME])
                ->join(["AA"=> AppraisalAssign::TABLE_NAME],"A.".Setup::APPRAISAL_ID."=AA.".AppraisalAssign::APPRAISAL_ID,[AppraisalAssign::APPRAISAL_ID])
                ->join(['E'=> HrEmployees::TABLE_NAME],"E.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::EMPLOYEE_ID,["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)"), HrEmployees::EMPLOYEE_ID])
                ->join(['T'=> Type::TABLE_NAME],"T.".Type::APPRAISAL_TYPE_ID."=A.". Setup::APPRAISAL_TYPE_ID,["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(T.APPRAISAL_TYPE_EDESC)")])
                ->join(['S'=> Stage::TABLE_NAME],"S.". Stage::STAGE_ID."=AA.". AppraisalAssign::CURRENT_STAGE_ID,["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)"),"STAGE_ID"]);
        
        $select->where([
            ("AA.".AppraisalAssign::REVIEWER_ID."=".$employeeId." OR AA.".AppraisalAssign::ALT_REVIEWER_ID."=".$employeeId),
            "AA.".AppraisalAssign::STATUS."='E'",
            "E.".HrEmployees::STATUS."='E'",
            "T.".Type::STATUS."='E'",
            "S.".Stage::STATUS."='E'",
            "( (COMPETENCIES_SETTING = (
  CASE
    WHEN (SELECT COUNT(*)
      FROM HRIS_APPRAISAL_ANSWER APNS
      WHERE A.APPRAISAL_ID = APNS.APPRAISAL_ID
      AND E.EMPLOYEE_ID    =APNS.EMPLOYEE_ID)=0
    THEN ('Y')
  END )
AND (SELECT COUNT(*)
  FROM HRIS_APPRAISAL_KPI APKPI
  WHERE A.APPRAISAL_ID = APKPI.APPRAISAL_ID
  AND E.EMPLOYEE_ID    =APKPI.EMPLOYEE_ID)>0)
OR (KPI_SETTING        = (
  CASE
    WHEN (SELECT COUNT(*)
      FROM HRIS_APPRAISAL_ANSWER APNS
      WHERE A.APPRAISAL_ID = APNS.APPRAISAL_ID
      AND E.EMPLOYEE_ID    =APNS.EMPLOYEE_ID)=0
    THEN ('Y')
  END)
AND (SELECT COUNT(*)
  FROM HRIS_APPRAISAL_COMPETENCY APCOM
  WHERE A.APPRAISAL_ID = APCOM.APPRAISAL_ID
  AND E.EMPLOYEE_ID    =APCOM.EMPLOYEE_ID)>0)
OR (SELECT COUNT(*)
  FROM HRIS_APPRAISAL_ANSWER APNS
  WHERE A.APPRAISAL_ID = APNS.APPRAISAL_ID
  AND E.EMPLOYEE_ID    = APNS.EMPLOYEE_ID
  )>0)"
            ]);
        $select->order("A.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

}