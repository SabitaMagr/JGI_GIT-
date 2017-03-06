<?php
namespace Appraisal\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Appraisal\Model\QuestionOption;
use Zend\Db\Sql\Select;
use Appraisal\Model\Question;
use Application\Repository\RepositoryInterface;

class QuestionOptionRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(QuestionOption::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([QuestionOption::STATUS=>'D'],[QuestionOption::OPTION_ID=>$id]);
    }
    public function deleteByQuestionId($questionId){
        $this->tableGateway->update([QuestionOption::STATUS=>'D'],[QuestionOption::QUESTION_ID=>$questionId]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[QuestionOption::OPTION_ID]);
        unset($data[QuestionOption::CREATED_DATE]);
        unset($data[QuestionOption::STATUS]);
        $this->tableGateway->update($data,[QuestionOption::OPTION_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByQuestionId($questionId){
        //return $this->tableGateway->select([QuestionOption::QUESTION_ID=>$questionId, QuestionOption::STATUS=>'E']);
        return $rowset= $this->tableGateway->select(function(Select $select) use($questionId) {
            $select->where([QuestionOption::STATUS=>'E',QuestionOption::QUESTION_ID=>$questionId]);
            $select->order(QuestionOption::OPTION_ID." ASC");
        });
    }
}
