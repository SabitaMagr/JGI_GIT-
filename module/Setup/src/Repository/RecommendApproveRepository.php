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
        $this->employeeTableGateway = new TableGateway("HR_EMPLOYEES", $adapter);
        $this->adapter = $adapter;
    }

    public function getDesignationList($employeeId)
    {
        $sql = "SELECT  DESIGNATION_ID, DESIGNATION_TITLE, PARENT_DESIGNATION, WITHIN_BRANCH, WITHIN_DEPARTMENT, LEVEL 
                FROM HR_DESIGNATIONS WHERE (LEVEL=2 OR LEVEL=3)
                START WITH DESIGNATION_ID = (SELECT E.DESIGNATION_ID FROM HR_EMPLOYEES E WHERE E.EMPLOYEE_ID=".$employeeId.")
                CONNECT BY PRIOR  PARENT_DESIGNATION=DESIGNATION_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    //to get recommender and approver based on designation and branch id
    public function getEmployeeList($withinBranch,$withinDepartment,$designationId,$branchId,$departmentId){
        $sql = "SELECT EMPLOYEE_ID,FIRST_NAME,MIDDLE_NAME,LAST_NAME FROM HR_EMPLOYEES WHERE STATUS='E' AND DESIGNATION_ID=".$designationId;

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
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=RA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=RA.RECOMMEND_BY",['FIRST_NAME_R'=>"FIRST_NAME","MIDDLE_NAME_R"=>'MIDDLE_NAME',"LAST_NAME_R"=>'LAST_NAME'],"left")
            ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=RA.APPROVED_BY",['FIRST_NAME_A'=>"FIRST_NAME","MIDDLE_NAME_A"=>'MIDDLE_NAME',"LAST_NAME_A"=>'LAST_NAME'],"left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "E1.STATUS='E'",
            "E2.STATUS='E'"
        ]);

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
        $sql = "SELECT EMPLOYEE_ID,FIRST_NAME,MIDDLE_NAME,LAST_NAME FROM 
                HR_EMPLOYEES WHERE STATUS='E' 
                AND EMPLOYEE_ID NOT IN 
                (SELECT EMPLOYEE_ID FROM HR_RECOMMENDER_APPROVER WHERE STATUS='E')";

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
            ->join(['E'=>"HR_EMPLOYEES"],"E.EMPLOYEE_ID=RA.EMPLOYEE_ID",['FIRST_NAME','MIDDLE_NAME','LAST_NAME'])
            ->join(['E1'=>"HR_EMPLOYEES"],"E1.EMPLOYEE_ID=RA.RECOMMEND_BY",['FIRST_NAME_R'=>"FIRST_NAME","MIDDLE_NAME_R"=>'MIDDLE_NAME',"LAST_NAME_R"=>'LAST_NAME'],"left")
            ->join(['E2'=>"HR_EMPLOYEES"],"E2.EMPLOYEE_ID=RA.APPROVED_BY",['FIRST_NAME_A'=>"FIRST_NAME","MIDDLE_NAME_A"=>'MIDDLE_NAME',"LAST_NAME_A"=>'LAST_NAME'],"left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "RA.EMPLOYEE_ID=".$employeeId,           
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
}