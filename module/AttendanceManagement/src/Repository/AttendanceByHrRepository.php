<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 3:38 PM
 */
namespace  AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use AttendanceManagement\Model\AttendanceByHr;
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Zend\Db\Sql\Expression;

class AttendanceByHrRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(AttendanceByHr::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AttendanceByHr::ID => $id]);
    }
    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"),new Expression("A.IN_REMARKS AS IN_REMARKS"),new Expression("A.OUT_REMARKS AS OUT_REMARKS")], true);
        $select->from(['A'=>AttendanceByHr::TABLE_NAME])
            ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;

    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([new Expression("TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY') AS ATTENDANCE_DT"),new Expression("TO_CHAR(A.IN_TIME, 'HH:MI AM') AS IN_TIME"),new Expression("TO_CHAR(A.OUT_TIME, 'HH:MI AM') AS OUT_TIME"), new Expression("E.EMPLOYEE_ID AS EMPLOYEE_ID"), new Expression("A.ID AS ID"),new Expression("A.IN_REMARKS AS IN_REMARKS"),new Expression("A.OUT_REMARKS AS OUT_REMARKS"),new Expression("A.TOTAL_HOUR AS TOTAL_HOUR")], true);
        $select->from(['A'=>AttendanceByHr::TABLE_NAME])
            ->join(['E' => 'HR_EMPLOYEES'], 'A.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => 'FIRST_NAME']);
        $select->where([AttendanceByHr::ID=>$id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function delete($id)
    {

    }


}