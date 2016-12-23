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
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\AcademicProgram;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class AcademicProgramRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(AcademicProgram::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,[AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function(Select $select){
            $select->where([AcademicProgram::STATUS=>'E']);
            $select->order(AcademicProgram::ACADEMIC_PROGRAM_NAME);
        });
    }


    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([AcademicProgram::STATUS=>'D'],[AcademicProgram::ACADEMIC_PROGRAM_ID=>$id]);
    }
}