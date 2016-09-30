<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 4:35 PM
 */
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use HolidayManagement\Model\Holiday;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use HolidayManagement\Model\HolidayBranch;


class HolidayRepository implements RepositoryInterface
{
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Holiday::TABLE_NAME, $adapter);
        $this->tableGatewayHolidayBranch = new TableGateway(HolidayBranch::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

   function add(Model $model)
   {
       // TODO: Implement add() method.
   }
   function edit(Model $model, $id)
   {
       // TODO: Implement edit() method.
   }
   function delete($id)
   {
       // TODO: Implement delete() method.
   }
   function fetchAll()
   {

   }
   function selectAll($employeeId){
       $sql = new Sql($this->adapter);
       $select = $sql->select();
       $select->columns([
           new Expression("TO_CHAR(H.START_DATE, 'DD-MON-YYYY') AS START_DATE"),
           new Expression("TO_CHAR(H.END_DATE, 'DD-MON-YYYY') AS END_DATE"),
           new Expression("H.HOLIDAY_ID AS HOLIDAY_ID"),
           new Expression("H.HOLIDAY_CODE AS HOLIDAY_CODE"),
           new Expression("H.HOLIDAY_ENAME AS HOLIDAY_ENAME"),
           new Expression("H.HALFDAY AS HALFDAY"),
           new Expression("H.FISCAL_YEAR AS FISCAL_YEAR"),
           new Expression("H.REMARKS AS REMARKS"),
       ], true);
       $select->from(['H' => Holiday::TABLE_NAME])
           ->join(['G' => 'HR_GENDERS'], 'H.GENDER_ID=G.GENDER_ID', ['GENDER_NAME'])
           ->join(['E' => 'HR_EMPLOYEES'], 'E.GENDER_ID=G.GENDER_ID', ['GENDER_ID'])
           ->join(['HB' => 'HR_HOLIDAY_BRANCH'], 'HB.HOLIDAY_ID=H.HOLIDAY_ID', ['BRANCH_ID']);

       $select->where(["H.STATUS='E'","HB.BRANCH_ID=E.BRANCH_ID"]);
       $statement = $sql->prepareStatementForSqlObject($select);
       print_r($statement->getSql());die();

       $result = $statement->execute();
       return $result;
   }
   function fetchById($id)
   {
       // TODO: Implement fetchById() method.
   }
}