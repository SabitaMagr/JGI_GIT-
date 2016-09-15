<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/14/16
 * Time: 4:48 PM
 */

namespace Application\Helper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class EntityHelper
{
    public static function getTableKVList(AdapterInterface $adapter, $tableName, $key, array $values, $where = null, $concatWith = null)
    {
        $gateway = new TableGateway($tableName, $adapter);

        if ($where == null) {
            $resultset = $gateway->select();
        } else {
            $resultset = $gateway->select($where);
        }
        $concatWith = ($concatWith == null) ? " " : $concatWith;

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $concattedValue = "";
            for($i=0; $i<count($values);$i++) {
                if($i==0){
                $concattedValue = $result[$values[$i]];
                    continue;
                }
                $concattedValue = $concattedValue . $concatWith . $result[$values[$i]];
            }
            $entitiesArray[$result[$key]] = $concattedValue;
        }
        return $entitiesArray;
    }
}