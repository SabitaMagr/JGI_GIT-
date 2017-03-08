<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Select\Select;
use Zend\Db\Select\Expression;
use Appraisal\Model\StageQuestion;
use Application\Model\Model;

class StageQuestionRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter){
        $this->tableGateway = new TableGateway(StageQuestion::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }
    public function add(Model $model){
        $this->tableGatway->insert($model->getArrayCopyForDb());
    }
    public function edit(Model $model,$id){
        //$data = $model->getArrayCopyForDb();
        //unset($data[''])
    }
    public function delete($id){
        $this->tableGateway->update(
            [StageQuestion::STATUS=>'D'],
            [
                StageQuestion::STAGE_ID=>$combo['STAGE_ID'],
                StageQuestion::QUESTION_ID=>$combo['QUESTION_ID']
                ]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

}