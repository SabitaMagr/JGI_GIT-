<?php

namespace Application\Helper;

use ReflectionClass;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EntityHelper {

    const STATUS_ENABLED = 'E';
    const STATUS_DISABLED = 'D';

    public static function getTableKVList(AdapterInterface $adapter, $tableName, $key = null, array $values, $where = null, $concatWith = null, $emptyColumn = false) {
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

    public static function rawQueryResult(AdapterInterface $adapter, string $sql) {
        $statement = $adapter->query($sql);
        return $statement->execute();
    }

    public static function getColumnNameArrayWithInitCaps(string $requestedName, array $columnList, string $shortForm = null, $selectedOnly = false) {
        $refl = new ReflectionClass($requestedName);
        $table = $refl->newInstanceArgs();

        $objAttrs = array_keys(get_object_vars($table));
        $objCols = [];

        foreach ($objAttrs as $objAttr) {
            if ('mappings' === $objAttr) {
                continue;
            }
            $tempCol = $table->mappings[$objAttr];
            if (in_array($tempCol, $columnList)) {
                array_push($objCols, Helper::columnExpression($tempCol, $shortForm, 'INITCAP'));
                continue;
            }
            if (!$selectedOnly) {
                array_push($objCols, $tempCol);
            }
        }

        return $objCols;
    }

}
