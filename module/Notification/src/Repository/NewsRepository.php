<?php

namespace Notification\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Notification\Model\NewsModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class NewsRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(NewsModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([NewsModel::STATUS=>'D'],[NewsModel::NEWS_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[NewsModel::CREATED_BY]);
        unset($data[NewsModel::CREATED_DT]);
        unset($data[NewsModel::STATUS]);
        $this->tableGateway->update($data,[NewsModel::NEWS_ID=>$id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select){
            $select->where([NewsModel::STATUS=>'E']);
            $select->order(NewsModel::NEWS_DATE." DESC");
        });
    }

    public function fetchById($id) {
//          $rowset = $this->tableGateway->select([NewsModel::NEWS_ID => $id, NewsModel::STATUS => 'E']);
//        return $result = $rowset->current();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['N' => NewsModel::TABLE_NAME]);
        $select->where(["N." . NewsModel::NEWS_ID . "='".$id."'"]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new NewsModel(), [
                    'newsDate',
                        ], NULL, 'N'), false);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
//        echo '<pre>';
//        print_r($result->current());
//        die();
        return $result->current();
    }

}
