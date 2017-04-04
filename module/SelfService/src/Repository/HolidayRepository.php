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
use Application\Helper\Helper;

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
       $today = Helper::getcurrentExpressionDate();
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
                ->join(['HB'=>HolidayBranch::TABLE_NAME],"HB.HOLIDAY_ID=H.HOLIDAY_ID",['HOLIDAY_ID'],"left")
                ->join(['E'=>'HRIS_EMPLOYEES'],"E.BRANCH_ID=HB.BRANCH_ID",['GENDER_ID'],"left");

       $select->where([
           "H.STATUS='E'",
           "E.EMPLOYEE_ID=".$employeeId,
           "H.END_DATE<=".$today->getExpression(),
           "((H.GENDER_ID IS NOT NULL AND H.GENDER_ID=E.GENDER_ID) OR H.GENDER_ID IS NULL)"
       ]);
       
       $select->order("H.START_DATE DESC");
       $statement = $sql->prepareStatementForSqlObject($select);
       $result = $statement->execute();
       return $result;
   }
   function fetchById($id)
   {
       // TODO: Implement fetchById() method.

   }
}