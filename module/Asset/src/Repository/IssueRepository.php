<?php

namespace Asset\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Issue;
use Asset\Model\Setup;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class IssueRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Issue::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
//        return true;
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        
//        echo '<pre>';
//        print_r($data);
//        die();
        
        unset($data[Issue::ISSUE_ID]);
        unset($data[Issue::CREATED_DATE]);
        unset($data[Issue::STATUS]);
        return $this->tableGateway->update($data, [Issue::ISSUE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, null, null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME])
                ->join(['S' => Setup::TABLE_NAME], 'S.' . Setup::ASSET_ID . '=AI.' . Issue::ASSET_ID, ["ASSET_EDESC" => new Expression("INITCAP(S.ASSET_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], 'E.' . HrEmployees::EMPLOYEE_ID . '=AI.' . Issue::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left");

        $select->where(["AI." . Issue::STATUS . "='E'"]);
//        $select->order("A." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function fetchAllById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class, null, null, null, null, null, "AI"), false);
        $select->from(['AI' => Issue::TABLE_NAME])
                ->join(['S' => Setup::TABLE_NAME], 'S.' . Setup::ASSET_ID . '=AI.' . Issue::ASSET_ID, ["ASSET_EDESC" => new Expression("INITCAP(S.ASSET_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], 'E.' . HrEmployees::EMPLOYEE_ID . '=AI.' . Issue::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left");

        $select->where(["AI." . Issue::STATUS . "='E'"]);
        $select->where("(AI.RETURNED!='Y' OR AI.RETURNED IS NULL)");
        $select->where(["AI." . Issue::ASSET_ID . "=$id"]);
//        $select->order("S." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Issue::class,
                null,
                [Issue::ISSUE_DATE,Issue::REQUEST_DATE,Issue::RETURN_DATE],null,null,null,"AI"),false);
        $select->from(['AI' => Issue::TABLE_NAME]);
        $select->where(["AI." . Issue::ISSUE_ID . "='".$id."'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
        
    }

    public function fetchallIssuableAsset() {

        $sql = "SELECT * FROM HRIS_ASSET_SETUP WHERE QUANTITY_BALANCE>0 AND STATUS='E' ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $list['A'] = [];
        $list['B'] = [];
        foreach ($result as $row) {
            $list['A'][$row['ASSET_ID']] = $row;
            $list['B'][$row['ASSET_ID']] = $row['ASSET_EDESC'];
        }
        return $list;
    }

    public function fetchAssetRemBalance($id) {
        $sql = "SELECT * FROM HRIS_ASSET_SETUP ";
        $sql .= "WHERE ASSET_ID='$id'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
