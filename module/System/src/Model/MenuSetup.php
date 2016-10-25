<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/18/16
 * Time: 2:05 PM
 */
namespace System\Model;

use Application\Model\Model;

class MenuSetup extends Model {
    const TABLE_NAME = "HR_MENUS";
    const MENU_ID = 'MENU_ID';
    const MENU_CODE = 'MENU_CODE';
    const MENU_NAME = 'MENU_NAME';
    const ROUTE = 'ROUTE';
    const ACTION = 'ACTION';
    const PARENT_MENU = 'PARENT_MENU';
    const MENU_DESCRIPTION = 'MENU_DESCRIPTION';
    const STATUS = 'STATUS';
    const CREATED_DT = 'CREATED_DT';
    const MODIFIED_DT = 'MODIFIED_DT';
    const ICON_CLASS = "ICON_CLASS";

    public $menuId;
    public $menuCode;
    public $menuName;
    public $route;
    public $action;
    public $parentMenu;
    public $menuDescription;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $iconClass;

    public $mappings = [
        'menuId'=>self::MENU_ID,
        'menuCode'=>self::MENU_CODE,
        'menuName'=>self::MENU_NAME,
        'route'=>self::ROUTE,
        'action'=>self::ACTION,
        'parentMenu'=>self::PARENT_MENU,
        'menuDescription'=>self::MENU_DESCRIPTION,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'iconClass'=>self::ICON_CLASS
    ];
}