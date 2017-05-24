<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Setup\Model\HrEmployees;
use Appraisal\Model\AppraisalAnswer;

class AppraisalAnswerRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalAnswer::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[AppraisalAnswer::ANSWER_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByAllDtl($appraisalId,$questionId,$employeeId,$userId){
        $result = $this->tableGateway->select([AppraisalAnswer::APPRAISAL_ID=>$appraisalId, AppraisalAnswer::QUESTION_ID=>$questionId,AppraisalAnswer::EMPLOYEE_ID=>$employeeId,AppraisalAnswer::USER_ID=>$userId]);
        return $result->current();
    }
    public function fetchByEmpAppraisalId($employeeId,$appraisalId){
        $result = $this->tableGateway->select([AppraisalAnswer::APPRAISAL_ID=>$appraisalId, AppraisalAnswer::EMPLOYEE_ID=>$employeeId]);
        return $result->current(); 
    }
}
