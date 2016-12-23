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
use Setup\Model\AcademicUniversity;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class AcademicUniversityRepository implements RepositoryInterface {

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(AcademicUniversity::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,[AcademicUniversity::ACADEMIC_UNIVERSITY_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function(Select $select){
            $select->where([AcademicUniversity::STATUS=>'E']);
            $select->order(AcademicUniversity::ACADEMIC_UNIVERSITY_NAME." ASC");
        });
    }


    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([AcademicUniversity::ACADEMIC_UNIVERSITY_ID=>$id]);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([AcademicUniversity::STATUS=>'D'],[AcademicUniversity::ACADEMIC_UNIVERSITY_ID=>$id]);
    }
}