<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/14/16
 * Time: 4:48 PM
 */

namespace Application\Helper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class EntityHelper {

    public static function getTableKVList(AdapterInterface $adapter, $tableName, $key = null, array $values, $where = null, $concatWith = null, $emptyColumn = null) {      
        $gateway = new TableGateway($tableName, $adapter);
        
        if ($where == null) {
            $resultset = $gateway->select();
        } else {
            $resultset = $gateway->select($where);
        }
        $concatWith = ($concatWith == null) ? " " : ($concatWith == null) ? "" : $concatWith;

        $entitiesArray = array();
        if ($emptyColumn) {
            $entitiesArray[null] = "----";
        }
        foreach ($resultset as $result) {
            $concattedValue = "";
            for ($i = 0; $i < count($values); $i++) {
                if ($i == 0) {
                    $concattedValue = $result[$values[$i]];
                    continue;
                }
                $concattedValue = $concattedValue . $concatWith . $result[$values[$i]];
            }
            if ($key == null) {
                array_push($entitiesArray, $concattedValue);
            } else {
                $entitiesArray[$result[$key]] = $concattedValue;
            }
        }
        return $entitiesArray;
    }

    public static function getTableKVListWithSortOption(AdapterInterface $adapter, $tableName, $key, array $values, $where = null, $orderBy = null, $orderAs = null, $concatWith = null, $emptyColumn = false) {
        $gateway = new TableGateway($tableName, $adapter);

        $resultset = $gateway->select(function(Select $select) use($where, $orderBy, $orderAs) {
            if ($select != null) {
                $select->where($where);
            }
            if ($orderBy != null) {
                $orderAs = ($orderAs != null) ? $orderAs : "";
                $select->order($orderBy . " " . $orderAs);
            }
        });
        $concatWith = ($concatWith == null) ? " " : ($concatWith == null) ? "" : $concatWith;

        $entitiesArray = array();
        if ($emptyColumn) {
            $entitiesArray[null] = "----";
        }
        foreach ($resultset as $result) {
            $concattedValue = "";
            for ($i = 0; $i < count($values); $i++) {
                if ($i == 0) {
                    $concattedValue = $result[$values[$i]];
                    continue;
                }
                $concattedValue = $concattedValue . $concatWith . $result[$values[$i]];
            }
            $entitiesArray[$result[$key]] = $concattedValue;
        }
        return $entitiesArray;
    }

    public static function getColumnsList(AdapterInterface $adapter, $holidayId, $key, array $values, $concatWith = null) {

        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from(['HB' => 'HR_HOLIDAY_BRANCH'])
                ->join(['B' => "HR_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["HB.HOLIDAY_ID" => $holidayId]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();

        $concatWith = ($concatWith == null) ? " " : $concatWith;

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $concattedValue = "";
            for ($i = 0; $i < count($values); $i++) {
                if ($i == 0) {
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
