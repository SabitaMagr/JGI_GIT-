<?php
namespace SelfService\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use SelfService\Model\OvertimeDetail;
use Application\Repository\RepositoryInterface;
use Application\Helper\EntityHelper;
use Application\Model\Model;

class OvertimeDetailRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(OvertimeDetail::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->insert($tempData);
    }

    public function delete($id) {
        $this->tableGateway->update([OvertimeDetail::STATUS=>'D'],[OvertimeDetail::DETAIL_ID=>$id]);
    }
    public function deleteByOvertimeId($overtimeId){
        $this->tableGateway->update([OvertimeDetail::STATUS=>'D'],[OvertimeDetail::OVERTIME_ID=>$overtimeId]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[OvertimeDetail::DETAIL_ID]);
        unset($data[OvertimeDetail::CREATED_DATE]);
        unset($data[OvertimeDetail::STATUS]);
        $this->tableGateway->update($data,[OvertimeDetail::DETAIL_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByOvertimeId($overtimeId){
        return $rowset= $this->tableGateway->select(function(Select $select) use($overtimeId) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(OvertimeDetail::class,null,[OvertimeDetail::CREATED_DATE, OvertimeDetail::MODIFIED_DATE],[OvertimeDetail::START_TIME, OvertimeDetail::END_TIME],null,null,null,false,false,[OvertimeDetail::TOTAL_HOUR]),false);
            $select->where([OvertimeDetail::STATUS=>'E',OvertimeDetail::OVERTIME_ID=>$overtimeId]);
            $select->order(OvertimeDetail::DETAIL_ID." ASC");
        });
    }
}
