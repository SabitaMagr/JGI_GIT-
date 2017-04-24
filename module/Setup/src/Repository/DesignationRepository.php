<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Designation::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function fetchAll()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Designation::class,
                [Designation::DESIGNATION_TITLE],
                NULL, NULL, NULL, NULL,'D1'),false);
        $select->from(["D1" => Designation::TABLE_NAME])
                ->join(["D2" => Designation::TABLE_NAME],'D1.PARENT_DESIGNATION=D2.DESIGNATION_ID',["PARENT_DESIGNATION_TITLE"=>new Expression('INITCAP(D2.DESIGNATION_TITLE)')],"left");
        $select->where(["D1.STATUS= 'E'"]);
        $select->order("D1.".Designation::DESIGNATION_TITLE." ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
        //print_r($statement->getSql()); die();
        ///return $this->tableGateway->select([Designation::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select(function(Select $select)use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Designation::class, [Designation::DESIGNATION_TITLE]), false);
            $select->where([Designation::DESIGNATION_ID => $id,Designation::STATUS=>'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Designation::DESIGNATION_ID => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->update([Designation::STATUS=>'D'],["DESIGNATION_ID" => $id]);
    }

}