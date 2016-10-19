<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/18/16
 * Time: 2:14 PM
 */
namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\MenuSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class MenuSetupRepository implements RepositoryInterface
{
    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(MenuSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(), [MenuSetup::MENU_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([MenuSetup::STATUS => "E"]);
    }

    public function fetchById($id)
    {
        $result = $this->tableGateway->select([MenuSetup::MENU_ID => $id]);
        return $result->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([MenuSetup::STATUS => "D"], [MenuSetup::MENU_ID => $id]);
    }

    public function getMenuList($id)
    {
        $sql = "SELECT * FROM HR_MENUS WHERE STATUS='E'";

        $statement = $this->adapter->query($sql);
        $resultset = $statement->execute();

        $entitiesArray = array();
        $entitiesArray[-1] = "None";
        foreach ($resultset as $result) {
            If ($id != $result['MENU_ID']) {
                $entitiesArray[$result['MENU_ID']] = $result['MENU_NAME'];
            }
        }
        return $entitiesArray;
    }

    public function getHierarchicalMenu($parent_menu = null)
    {
        $where = "";
        if ($parent_menu == null) {
            $where .= " AND LEVEL=1";
        } else {
            $where .= " AND PARENT_MENU=" . $parent_menu;
        }

        $sql = "SELECT MENU_NAME,MENU_ID ,PARENT_MENU,URL, LEVEL,CONNECT_BY_ISLEAF is_leaf FROM HR_MENUS WHERE STATUS = 'E'" . $where . " CONNECT BY PRIOR MENU_ID = PARENT_MENU START WITH PARENT_MENU=-1";

        $statement = $this->adapter->query($sql);
        $resultset = $statement->execute();
        return $resultset;
    }
}