<?php

namespace Application\Helper;

use Setup\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class Helper
{
    const ORACLE_DATE_FORMAT="DD-MON-YYYY";
    const MYSQL_DATE_FORMAT="";
    const PHP_DATE_FORMAT="d-M-Y";

    public static function addFlashMessagesToArray($context, $return)
    {
        $flashMessenger = $context->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return['messages'] = $flashMessenger->getMessages();
        }
        return $return;
    }

    public static function getMaxId(AdapterInterface $adapter, $tableName, $columnName)
    {
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($tableName);
        $select->columns(["MAX({$columnName}) AS MAX_{$columnName}"], false);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $row = $result->current();
        return $row["MAX_{$columnName}"];

    }

    public static function convertColumnDateFormat(AdapterInterface $adapter, Model $table, $attrs)
    {
        $format = 'DD-MON-YYYY HH24:MI:SS';

        $temp = get_object_vars($table);
        foreach ($attrs as $attr) {
            unset($temp[$attr]);
        }
        unset($temp['mappings']);

        $attributes = array_keys($temp);
        $tempCols = [];
        foreach ($attrs as $attr) {
            array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT));
        }
        foreach ($attributes as $attribute) {
            array_push($tempCols, $table->mappings[$attribute]);
        }


        return $tempCols;
    }

    public static function appendDateFormat($adapter, $columnName, $format)
    {

        $tempStr = "";
        switch (strtolower($adapter->getPlatform()->getName())) {
            case strtolower("Oracle"):
                $tempStr = "TO_CHAR({$columnName}, '{$format}') AS {$columnName}";
                break;

            case strtolower("Mysql"):

                break;
        }
        return $tempStr;


    }

    public static function getExpressionDate($dateStr){
        $format=Helper::ORACLE_DATE_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', '{$format}')");
    }

    public static function getcurrentExpressionDate(){
        $currentDate=date(self::PHP_DATE_FORMAT);
        return self::getExpressionDate($currentDate);
    }
}