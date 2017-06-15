<?php
namespace Appraisal\Repository;

use Appraisal\Model\AppraisalAssign;
use Appraisal\Model\Setup;
use Setup\Model\HrEmployees;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\Type;
use Appraisal\Model\Stage;

class AppraisalAssignRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AppraisalAssign::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[AppraisalAssign::CREATED_DATE]);
        unset($data[AppraisalAssign::STATUS]);
        $this->tableGateway->update($data,[AppraisalAssign::EMPLOYEE_ID=>$id[0],AppraisalAssign::APPRAISAL_ID=>$id[1]]); 
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function getDetailByEmpAppraisalId($employeeId,$appraisalId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("AA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AA.APPRAISAL_ID AS APPRAISAL_ID"),
            new Expression("AA.STATUS AS STATUS"),
            new Expression("AA.REVIEWER_ID AS REVIEWER_ID"),
            new Expression("AA.APPRAISER_ID AS APPRAISER_ID"),
            new Expression("AA.REMARKS AS REMARKS")
        ]);
        $select->from(["AA"=>AppraisalAssign::TABLE_NAME])
                ->join(["A"=> Setup::TABLE_NAME],"A.".Setup::APPRAISAL_ID."=AA.".AppraisalAssign::APPRAISAL_ID,["APPRAISAL_EDESC"=>new Expression("INITCAP(A.APPRAISAL_EDESC)")],"left")
                ->join(['E'=> HrEmployees::TABLE_NAME],"E.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::EMPLOYEE_ID,["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)")],"left")
                ->join(['E1'=> HrEmployees::TABLE_NAME],"E1.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::REVIEWER_ID,['FIRST_NAME_R'=>new Expression("INITCAP(E1.FIRST_NAME)"),"MIDDLE_NAME_R"=>new Expression("INITCAP(E1.MIDDLE_NAME)"),"LAST_NAME_R"=>new Expression("INITCAP(E1.LAST_NAME)"),"RETIRED_R"=> HrEmployees::RETIRED_FLAG,"STATUS_R"=> HrEmployees::STATUS],"left")
                ->join(['E2'=> HrEmployees::TABLE_NAME],"E2.". HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::APPRAISER_ID,['FIRST_NAME_A'=>new Expression("INITCAP(E2.FIRST_NAME)"),"MIDDLE_NAME_A"=>new Expression("INITCAP(E2.MIDDLE_NAME)"),"LAST_NAME_A"=>new Expression("INITCAP(E2.LAST_NAME)"),"RETIRED_A"=>HrEmployees::RETIRED_FLAG,"STATUS_A"=>HrEmployees::STATUS],"left");
        
        $select->where([
            "AA.".AppraisalAssign::APPRAISAL_ID."=".$appraisalId,
            "AA.".AppraisalAssign::EMPLOYEE_ID."=".$employeeId,
            "AA.".AppraisalAssign::STATUS."='E' AND
  (((E1.STATUS =
    CASE
      WHEN E1.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E1.STATUS IS NULL)
  AND
  (E1.RETIRED_FLAG =
    CASE
      WHEN E1.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E1.RETIRED_FLAG IS NULL))
OR
  ((E2.STATUS =
    CASE
      WHEN E2.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E2.STATUS IS NULL)
AND
  (E2.RETIRED_FLAG =
    CASE
      WHEN E2.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E2.RETIRED_FLAG IS NULL)))"
        ]);
        $select->order("E.".HrEmployees::FIRST_NAME." ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function fetchByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("A.APPRAISAL_ID AS APPRAISAL_ID"),
            new Expression("A.APPRAISAL_TYPE_ID AS APPRAISAL_TYPE_ID"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("A.APPRAISAL_EDESC AS APPRAISAL_EDESC"),
            new Expression("A.REMARKS AS REMARKS"),
            new Expression("INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE"), 
            new Expression("INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
        ]);
        $select->from(["A"=>Setup::TABLE_NAME])
                ->join(["AA"=> AppraisalAssign::TABLE_NAME],"A.".Setup::APPRAISAL_ID."=AA.".AppraisalAssign::APPRAISAL_ID,[AppraisalAssign::APPRAISAL_ID])
                ->join(['E'=> HrEmployees::TABLE_NAME],"E.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::EMPLOYEE_ID,["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)"), HrEmployees::EMPLOYEE_ID])
                ->join(['T'=> Type::TABLE_NAME],"T.".Type::APPRAISAL_TYPE_ID."=A.". Setup::APPRAISAL_TYPE_ID,["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(T.APPRAISAL_TYPE_EDESC)")])
                ->join(['S'=> Stage::TABLE_NAME],"S.". Stage::STAGE_ID."=AA.". AppraisalAssign::CURRENT_STAGE_ID,["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)"),"STAGE_ORDER_NO"=>"ORDER_NO"]);
        
        $select->where([
            "AA.".AppraisalAssign::EMPLOYEE_ID."=".$employeeId,
            "AA.".AppraisalAssign::STATUS."='E'",
            "E.".HrEmployees::STATUS."='E'",
            "T.".Type::STATUS."='E'",
            "S.".Stage::STATUS."='E'"]);
        $select->order("A.".Setup::APPRAISAL_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function getEmployeeAppraisalDetail($employeeId,$appraisalId){
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
                ->join(["AA"=> AppraisalAssign::TABLE_NAME],"A.".Setup::APPRAISAL_ID."=AA.".AppraisalAssign::APPRAISAL_ID,[AppraisalAssign::APPRAISAL_ID,AppraisalAssign::APPRAISER_ID,AppraisalAssign::REVIEWER_ID, AppraisalAssign::ANNUAL_RATING_COMPETENCY, AppraisalAssign::ANNUAL_RATING_KPI, AppraisalAssign::APPRAISER_OVERALL_RATING])
                ->join(['E'=> HrEmployees::TABLE_NAME],"E.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::EMPLOYEE_ID,["FIRST_NAME"=>new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME"=>new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME"=>new Expression("INITCAP(E.LAST_NAME)"), HrEmployees::EMPLOYEE_ID])
                ->join(['T'=> Type::TABLE_NAME],"T.".Type::APPRAISAL_TYPE_ID."=A.". Setup::APPRAISAL_TYPE_ID,["APPRAISAL_TYPE_EDESC"=>new Expression("INITCAP(T.APPRAISAL_TYPE_EDESC)")])
                ->join(['S'=> Stage::TABLE_NAME],"S.". Stage::STAGE_ID."=AA.". AppraisalAssign::CURRENT_STAGE_ID,["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)"),"STAGE_ORDER_NO"=>"ORDER_NO","STAGE_ID"])
                ->join(['E1'=> HrEmployees::TABLE_NAME],"E1.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::APPRAISER_ID,["FIRST_NAME_A"=>new Expression("INITCAP(E1.FIRST_NAME)"), "MIDDLE_NAME_A"=>new Expression("INITCAP(E1.MIDDLE_NAME)"), "LAST_NAME_A"=>new Expression("INITCAP(E1.LAST_NAME)"),"EMPLOYEE_ID_A"=> HrEmployees::EMPLOYEE_ID],"left")
                ->join(['E2'=> HrEmployees::TABLE_NAME],"E2.".HrEmployees::EMPLOYEE_ID."=AA.". AppraisalAssign::REVIEWER_ID,["FIRST_NAME_R"=>new Expression("INITCAP(E2.FIRST_NAME)"),"MIDDLE_NAME_R"=>new Expression("INITCAP(E2.MIDDLE_NAME)"), "LAST_NAME_R"=>new Expression("INITCAP(E2.LAST_NAME)"), "EMPLOYEE_ID_R"=>HrEmployees::EMPLOYEE_ID],"left");
        
        $select->where([
            "AA.".AppraisalAssign::EMPLOYEE_ID."=".$employeeId,
            "AA.".AppraisalAssign::APPRAISAL_ID."=".$appraisalId,
            "AA.".AppraisalAssign::STATUS."='E'",
            "E.".HrEmployees::STATUS."='E'",
            "T.".Type::STATUS."='E'",
            "S.".Stage::STATUS."='E' AND
  (((E1.STATUS =
    CASE
      WHEN E1.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E1.STATUS IS NULL)
  AND
  (E1.RETIRED_FLAG =
    CASE
      WHEN E1.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E1.RETIRED_FLAG IS NULL))
OR
  ((E2.STATUS =
    CASE
      WHEN E2.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E2.STATUS IS NULL)
AND
  (E2.RETIRED_FLAG =
    CASE
      WHEN E2.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E2.RETIRED_FLAG IS NULL)))"
            ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function updateCurrentStageByAppId($stageId,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalAssign::CURRENT_STAGE_ID=>$stageId],[AppraisalAssign::APPRAISAL_ID=>$appraisalId,AppraisalAssign::EMPLOYEE_ID=>$employeeId,AppraisalAssign::STATUS=>'E']);
    }
    public function updateAnnualRatingId($annualRatingKPI,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalAssign::ANNUAL_RATING_KPI =>$annualRatingKPI],[AppraisalAssign::APPRAISAL_ID=>$appraisalId,AppraisalAssign::EMPLOYEE_ID=>$employeeId,AppraisalAssign::STATUS=>'E']);
    }
    public function updateAnnualRatingComId($annualRatingCompetency,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalAssign::ANNUAL_RATING_COMPETENCY =>$annualRatingCompetency],[AppraisalAssign::APPRAISAL_ID=>$appraisalId,AppraisalAssign::EMPLOYEE_ID=>$employeeId,AppraisalAssign::STATUS=>'E']);
    }
    public function updateOverallRatingId($overallRating,$appraisalId,$employeeId){
        $this->tableGateway->update([AppraisalAssign::APPRAISER_OVERALL_RATING =>$overallRating],[AppraisalAssign::APPRAISAL_ID=>$appraisalId,AppraisalAssign::EMPLOYEE_ID=>$employeeId,AppraisalAssign::STATUS=>'E']);
    }
}


