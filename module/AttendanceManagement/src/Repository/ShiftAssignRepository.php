<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:31 PM
 */

namespace AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use AttendanceManagement\Model\ShiftAssign;

class ShiftAssignRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(ShiftAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,
            [ShiftAssign::SHIFT_ASSIGN_ID=>$id]
            );
    }
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select([ShiftAssign::SHIFT_ASSIGN_ID=>$id,ShiftAssign::STATUS=>'E']);
        return $rowset->current();
    }
    public function delete($id)
    {
        $this->tableGateway->update([
            ShiftAssign::STATUS =>'D'
        ],[
            ShiftAssign::SHIFT_ASSIGN_ID=>$id
        ]);
    }


}