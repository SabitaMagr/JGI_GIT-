<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 3:00 PM
 */
namespace System\Repository;

use Application\Model\Model;
use Application\Model\User;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use System\Model\UserSetup;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class UserSetupRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(UserSetup::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[UserSetup::USER_ID=>$id]);
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("US.STATUS AS STATUS"),
            new Expression("US.USER_ID AS USER_ID"),
            new Expression("US.USER_NAME AS USER_NAME"),
            new Expression("US.PASSWORD AS PASSWORD"),
            new Expression("US.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("US.ROLE_ID AS ROLE_ID"),
        ], true);

        $select->from(['US' => UserSetup::TABLE_NAME])
            ->join(['E'=>"HRIS_EMPLOYEES"],"E.EMPLOYEE_ID=US.EMPLOYEE_ID",['FIRST_NAME'=>new Expression("INITCAP(E.FIRST_NAME)"),'MIDDLE_NAME'=>new Expression("INITCAP(E.MIDDLE_NAME)"),'LAST_NAME'=>new Expression("INITCAP(E.LAST_NAME)")])
            ->join(['R'=>'HRIS_ROLES'],"R.ROLE_ID=US.ROLE_ID",['ROLE_NAME']);

        $select->where([
            "US.STATUS='E'",
            "E.STATUS='E'"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    //to get the employee list for select option
    public function getEmployeeList($employeeId=null){

        $sql = "SELECT INITCAP(FIRST_NAME) AS FIRST_NAME,INITCAP(MIDDLE_NAME) AS MIDDLE_NAME, INITCAP(LAST_NAME) AS LAST_NAME,EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND RETIRED_FLAG='N' AND EMPLOYEE_ID NOT IN (SELECT EMPLOYEE_ID FROM HRIS_USERS WHERE STATUS='E'AND EMPLOYEE_ID IS NOT NULL)";

        if($employeeId!=null){
            $sql .= " UNION 
SELECT INITCAP(FIRST_NAME) AS FIRST_NAME,INITCAP(MIDDLE_NAME) AS MIDDLE_NAME, INITCAP(LAST_NAME) AS LAST_NAME,EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID IN (".$employeeId.")";
        }

        $statement = $this->adapter->query($sql);
       // print_r($statement->getSql());die();
        $resultset = $statement->execute();

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result['EMPLOYEE_ID']] = $result['FIRST_NAME']." ".$result['MIDDLE_NAME']." ".$result['LAST_NAME'];
        }
        return $entitiesArray;
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([UserSetup::USER_ID=>$id]);
        return $result->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([UserSetup::STATUS=>"D"],[UserSetup::USER_ID=>$id]);
    }
}