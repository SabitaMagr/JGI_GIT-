<?php

namespace Application\Helper;

use ReflectionClass;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EntityHelper {

    const STATUS_ENABLED = 'E';
    const STATUS_DISABLED = 'D';
    const ORACLE_FUNCTION_INITCAP = 'INITCAP';

    public static function getTableKVList(AdapterInterface $adapter, $tableName, $key = null, array $values, $where = null, $concatWith = null, $emptyColumn = false, $orderBy = null, $orderAs = null, $initCap = false) {
        $gateway = new TableGateway($tableName, $adapter);
        $resultset = $gateway->select(function(Select $select) use ($key, $values, $where, $orderBy, $orderAs, $initCap) {
            if ($key !== null) {
                array_push($values, $key);
            }
            if ($initCap) {
                $tempValues = [];
                foreach ($values as $key => $value) {
                    $tempValues[$key] = "INITCAP({$value}) AS {$value}";
                }
                $values = $tempValues;
            }
            $select->columns($values, false);
            if ($where !== null) {
                $select->where($where);
            }

            if ($orderBy !== null) {
                $select->order([$orderBy => ($orderAs !== null) ? $orderAs : Select::ORDER_ASCENDING]);
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
            if ($key == null) {
                array_push($entitiesArray, $concattedValue);
            } else {
                $entitiesArray[$result[$key]] = $concattedValue;
            }
        }
        return $entitiesArray;
    }

    public static function getTableKVListWithSortOption(AdapterInterface $adapter, $tableName, $key, array $values, $where = null, $orderBy = null, $orderAs = null, $concatWith = null, $emptyColumn = false, $initCap = false) {
        return self::getTableKVList($adapter, $tableName, $key, $values, $where, $concatWith, $emptyColumn, $orderBy, $orderAs, $initCap);
    }

    public static function rawQueryResult(AdapterInterface $adapter, string $sql) {
        $statement = $adapter->query($sql);
        return $statement->execute();
    }

    public static function getColumnNameArrayWithOracleFns(string $requestedName, array $initCapColumnList = null, array $dateColumnList = null, array $timeColumnList = null, array $timeIntervalColumnList = null, array $otherColumnList = null, string $shortForm = null, $selectedOnly = false) {
        $refl = new ReflectionClass($requestedName);
        $table = $refl->newInstanceArgs();

        $objAttrs = array_keys(get_object_vars($table));
        $objCols = [];

        foreach ($objAttrs as $objAttr) {
            if ('mappings' === $objAttr) {
                continue;
            }
            $tempCol = $table->mappings[$objAttr];
            if ($initCapColumnList !== null && in_array($tempCol, $initCapColumnList)) {
                array_push($objCols, Helper::columnExpression($tempCol, $shortForm, self::ORACLE_FUNCTION_INITCAP));
                continue;
            }

            if ($dateColumnList !== null && in_array($tempCol, $dateColumnList)) {
                array_push($objCols, self::formatColumn($tempCol, $shortForm, Helper::ORACLE_DATE_FORMAT));
                continue;
            }
            if ($timeColumnList !== null && in_array($tempCol, $timeColumnList)) {
                array_push($objCols, self::formatColumn($tempCol, $shortForm, Helper::ORACLE_TIME_FORMAT));
                continue;
            }
            if ($timeIntervalColumnList !== null && in_array($tempCol, $timeIntervalColumnList)) {
                array_push($objCols, self::formatColumn($tempCol, $shortForm, Helper::ORACLE_TIMESTAMP_FORMAT));
                continue;
            }
            if ($otherColumnList !== null && in_array($tempCol, $otherColumnList)) {
                array_push($objCols, $tempCol);
                continue;
            }


            if (!$selectedOnly) {
                array_push($objCols, Helper::columnExpression($tempCol));
            }
        }

        return $objCols;
    }

    public static function formatColumn($columnName, $shortForm = null, $format) {
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        return "INITCAP(TO_CHAR({$pre}{$columnName}, '{$format}')) AS {$columnName}";
    }

}
