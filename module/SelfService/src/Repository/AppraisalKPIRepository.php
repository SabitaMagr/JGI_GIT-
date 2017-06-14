<?php
namespace SelfService\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\AppraisalKPI;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Authentication\AuthenticationService;

class AppraisalKPIRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    private $employeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalKPI::TABLE_NAME,$adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update(
                [
                    AppraisalKPI::STATUS=>'D', 
                    AppraisalKPI::MODIFIED_BY=>$this->employeeId, 
                    AppraisalKPI::MODIFIED_DATE=>Helper::getcurrentExpressionDate()
                ],
                [AppraisalKPI::SNO=>$id]
                );
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[AppraisalKPI::SNO=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByAppEmpId($employeeId,$appraisalId){
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->from(AppraisalKPI::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(AppraisalKPI::class,
                null,null,null,null,null),false);
        $select->where([AppraisalKPI::APPRAISAL_ID=>$appraisalId,AppraisalKPI::EMPLOYEE_ID=>$employeeId,AppraisalKPI::STATUS=>"E"]);
        $select->order(AppraisalKPI::SNO);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}

