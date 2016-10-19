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
    const URL = 'URL';
    const PARENT_MENU = 'PARENT_MENU';
    const MENU_DESCRIPTION = 'MENU_DESCRIPTION';
    const STATUS = 'STATUS';
    const CREATED_DT = 'CREATED_DT';
    const MODIFIED_DT = 'MODIFIED_DT';

    public $menuId;
    public $menuCode;
    public $menuName;
    public $url;
    public $parentMenu;
    public $menuDescription;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'menuId'=>self::MENU_ID,
        'menuCode'=>self::MENU_CODE,
        'menuName'=>self::MENU_NAME,
        'url'=>self::URL,
        'parentMenu'=>self::PARENT_MENU,
        'menuDescription'=>self::MENU_DESCRIPTION,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT
    ];
}