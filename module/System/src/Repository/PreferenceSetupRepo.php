<?php
namespace System\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\PreferenceSetup;
use Setup\Model\Company;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class PreferenceSetupRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(PreferenceSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [PreferenceSetup::PREFERENCE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select([PreferenceSetup::STATUS=>"E"]);
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([PreferenceSetup::PREFERENCE_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([PreferenceSetup::STATUS => 'D'], [PreferenceSetup::PREFERENCE_ID => $id]);
    }

}
