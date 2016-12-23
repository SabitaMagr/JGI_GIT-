<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/11/16
 * Time: 10:38 AM
 */
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\AcademicProgram;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\AcademicCourse;
use Zend\Db\Sql\Expression;

class AcademicCourseRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(AcademicCourse::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AcademicCourse::ACADEMIC_COURSE_ID => $id]);
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
                new Expression("AC.ACADEMIC_COURSE_ID AS ACADEMIC_COURSE_ID"),
                new Expression("AC.ACADEMIC_COURSE_CODE AS ACADEMIC_COURSE_CODE"),
                new Expression("AC.ACADEMIC_COURSE_NAME AS ACADEMIC_COURSE_NAME"),
                new Expression("AC.STATUS AS STATUS"),
                new Expression("AC.REMARKS AS REMARKS")
            ]
            , true);
        $select->from(['AC' => AcademicCourse::TABLE_NAME])
            ->join(['AP' => AcademicProgram::TABLE_NAME], 'AC.ACADEMIC_PROGRAM_ID=AP.ACADEMIC_PROGRAM_ID', ["ACADEMIC_PROGRAM_NAME" => 'ACADEMIC_PROGRAM_NAME'],"left");
        $select->where(["AC.STATUS='E'"]);
        $select->order("AC.ACADEMIC_COURSE_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        //print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select([AcademicCourse::ACADEMIC_COURSE_ID => $id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([AcademicCourse::STATUS => 'D'], [AcademicCourse::ACADEMIC_COURSE_ID => $id]);
    }
}