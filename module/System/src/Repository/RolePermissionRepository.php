<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/21/16
 * Time: 2:50 PM
 */
namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use System\Model\RolePermission;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class RolePermissionRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(RolePermission::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        // TODO: Implement edit() method.
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id)
    {
        // TODO: Implement fetchById() method.
    }

    public function delete($id)
    {

    }
    public function deleteAll($menuId,$roleId){
        $this->tableGateway->update(['STATUS'=>'D'],['MENU_ID'=>$menuId,'ROLE_ID'=>$roleId]);
    }
    public function findAllRoleByMenuId($menuId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RP.ROLE_ID AS ROLE_ID"),
            new Expression("RP.MENU_ID AS MENU_ID"),
        ], true);

        $select->from(['RP' => RolePermission::TABLE_NAME])
            ->join(['R'=>'HR_ROLES'],"R.ROLE_ID=RP.ROLE_ID",['ROLE_NAME']);

        $select->where([
            "RP.STATUS='E'",
            "RP.MENU_ID=".$menuId
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    public function selectRoleMenu($menuId,$roleId){
        return $this->tableGateway->select(['MENU_ID'=>$menuId,'ROLE_ID'=>$roleId]);
    }
    public function updateDetail($menuId,$roleId){
        $this->tableGateway->update(['STATUS'=>'E'],['MENU_ID'=>$menuId,'ROLE_ID'=>$roleId]);
    }
}