<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/3/16
 * Time: 1:27 PM
 */
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\RecommendApprove;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;

class RecommendApproveRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(RecommendApprove::TABLE_NAME, $adapter);
        $this->employeeTableGateway = new TableGateway("HRIS_EMPLOYEES", $adapter);
        $this->adapter = $adapter;
    }

    public function getDesignationList($employeeId)
    {
        $sql = "SELECT  DESIGNATION_ID, INITCAP(DESIGNATION_TITLE) AS DESIGNATION_TITLE, PARENT_DESIGNATION, WITHIN_BRANCH, WITHIN_DEPARTMENT, LEVEL 
                FROM HRIS_DESIGNATIONS WHERE (LEVEL=2 OR LEVEL=3)
                START WITH DESIGNATION_ID = (SELECT E.DESIGNATION_ID FROM HRIS_EMPLOYEES E WHERE E.EMPLOYEE_ID=".$employeeId.")
                CONNECT BY PRIOR  PARENT_DESIGNATION=DESIGNATION_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    //to get recommender and approver based on designation and branch id
    public function getEmployeeList($withinBranch,$withinDepartment,$designationId,$branchId,$departmentId){
        $sql = "SELECT EMPLOYEE_ID,INITCAP(FIRST_NAME) AS FIRST_NAME,INITCAP(MIDDLE_NAME) AS MIDDLE_NAME,INITCAP(LAST_NAME) AS LAST_NAME FROM HRIS_EMPLOYEES WHERE STATUS='E' AND RETIRED_FLAG='N' AND DESIGNATION_ID=".$designationId;

        if($withinBranch!=null && $withinBranch!="N"){
            $sql.=" AND BRANCH_ID=".$branchId;
        }

        if($withinDepartment!=null && $withinDepartment!="N"){
            $sql.=" AND DEPARTMENT_ID=".$departmentId;
        }

        $statement = $this->adapter->query($sql);
        $resultset = $statement->execute();

        return $resultset;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RA.STATUS AS STATUS"),
            new Expression("RA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("RA.RECOMMEND_BY AS RECOMMEND_BY"),
            new Expression("RA.APPROVED_BY AS APPROVED_BY"),
        ], true);
        $select->from(['RA' => RecommendApprove::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=RA.EMPLOYEE_ID",['FIRST_NAME'=>new Expression("INITCAP(E.FIRST_NAME)"),'MIDDLE_NAME'=>new Expression("INITCAP(E.MIDDLE_NAME)"),'LAST_NAME'=>new Expression("INITCAP(E.LAST_NAME)")],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=RA.RECOMMEND_BY",['FIRST_NAME_R'=>new Expression("INITCAP(E1.FIRST_NAME)"),"MIDDLE_NAME_R"=>new Expression("INITCAP(E1.MIDDLE_NAME)"),"LAST_NAME_R"=>new Expression("INITCAP(E1.LAST_NAME)"),"RETIRED_R"=>"RETIRED_FLAG","STATUS_R"=>"STATUS"],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=RA.APPROVED_BY",['FIRST_NAME_A'=>new Expression("INITCAP(E2.FIRST_NAME)"),"MIDDLE_NAME_A"=>new Expression("INITCAP(E2.MIDDLE_NAME)"),"LAST_NAME_A"=>new Expression("INITCAP(E2.LAST_NAME)"),"RETIRED_A"=>"RETIRED_FLAG","STATUS_A"=>"STATUS"],"left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N' AND
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
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    //to get the employee list for select option
    public function getEmployees($id=null){
        $entitiesArray = array();
        if($id!=null) {
            $empresult = $this->employeeTableGateway->select(['EMPLOYEE_ID' => $id])->current();
            $entitiesArray[$empresult['EMPLOYEE_ID']] = $empresult['FIRST_NAME'] . " " . $empresult['MIDDLE_NAME'] . " " . $empresult['LAST_NAME'];
        }
        $sql = "SELECT EMPLOYEE_ID,INITCAP(FIRST_NAME) AS FIRST_NAME,INITCAP(MIDDLE_NAME) AS MIDDLE_NAME,INITCAP(LAST_NAME) AS LAST_NAME FROM 
                HRIS_EMPLOYEES WHERE STATUS='E' AND RETIRED_FLAG='N'
                AND EMPLOYEE_ID NOT IN 
                (SELECT EMPLOYEE_ID FROM HRIS_RECOMMENDER_APPROVER WHERE STATUS='E')";

        $statement = $this->adapter->query($sql);
        $resultset = $statement->execute();

        foreach ($resultset as $result) {
            $entitiesArray[$result['EMPLOYEE_ID']] = $result['FIRST_NAME']." ".$result['MIDDLE_NAME']." ".$result['LAST_NAME'];
        }
        return $entitiesArray;
    }

    public function edit(Model $model,$id){
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,[RecommendApprove::EMPLOYEE_ID=>$id]);
    }
    public function delete($id){
        $this->tableGateway->update([RecommendApprove::STATUS=>'D'],[RecommendApprove::EMPLOYEE_ID => $id]);
    }

    public function fetchById($id){
        $row = $this->tableGateway->select([RecommendApprove::EMPLOYEE_ID=>$id]);
        return $row->current();
    }
    public function getDetailByEmployeeID($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RA.STATUS AS STATUS"),
            new Expression("RA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("RA.RECOMMEND_BY AS RECOMMEND_BY"),
            new Expression("RA.APPROVED_BY AS APPROVED_BY"),
        ], true);
        $select->from(['RA' => RecommendApprove::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=RA.EMPLOYEE_ID",['FIRST_NAME'=>new Expression("INITCAP(E.FIRST_NAME)"),'MIDDLE_NAME'=>new Expression("INITCAP(E.MIDDLE_NAME)"),'LAST_NAME'=>new Expression("INITCAP(E.LAST_NAME)")],"left")
            ->join(['E1'=>"HRIS_EMPLOYEES"],"E1.EMPLOYEE_ID=RA.RECOMMEND_BY",['FIRST_NAME_R'=>new Expression("INITCAP(E1.FIRST_NAME)"),"MIDDLE_NAME_R"=>new Expression("INITCAP(E1.MIDDLE_NAME)"),"LAST_NAME_R"=>new Expression("INITCAP(E1.LAST_NAME)"),"RETIRED_R"=>"RETIRED_FLAG","STATUS_R"=>"STATUS"],"left")
            ->join(['E2'=>"HRIS_EMPLOYEES"],"E2.EMPLOYEE_ID=RA.APPROVED_BY",['FIRST_NAME_A'=>new Expression("INITCAP(E2.FIRST_NAME)"),"MIDDLE_NAME_A"=>new Expression("INITCAP(E2.MIDDLE_NAME)"),"LAST_NAME_A"=>new Expression("INITCAP(E2.LAST_NAME)"),"RETIRED_A"=>"RETIRED_FLAG","STATUS_A"=>"STATUS"],"left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N'",
            "RA.EMPLOYEE_ID=".$employeeId." AND
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
        
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result->current();
    }
}