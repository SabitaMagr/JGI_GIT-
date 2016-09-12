<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 3:10 PM
 */

namespace LeaveManagement\Helper;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class EntityHelper
{
    const HR_LEAVE_MASTER_SETUP="HR_LEAVE_MASTER_SETUP";

    public static $tablesAttributes=[
        self::HR_LEAVE_MASTER_SETUP=>[
            "LEAVE_ID"=>"LEAVE_ENAME"
        ],
    ];

    public static function getTableKVList(AdapterInterface $adapter,$tableName,$id=null){
        $gateway = new TableGateway($tableName, $adapter);
        $key=array_keys(self::$tablesAttributes[$tableName])[0];
        $value=array_values(self::$tablesAttributes[$tableName])[0];

        if($id==null){
            $resultset = $gateway->select();
        }else{
            $resultset = $gateway->select($id);

        }

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result[$key]] = $result[$value];
        }
        return $entitiesArray;
    }


}