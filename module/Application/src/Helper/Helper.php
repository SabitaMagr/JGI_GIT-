<?php

namespace Application\Helper;

use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class Helper
{
    const ORACLE_DATE_FORMAT = "DD-MON-YYYY";
    const ORACLE_TIME_FORMAT = "HH:MI AM";
    const MYSQL_DATE_FORMAT = "";
    const PHP_DATE_FORMAT = "d-M-Y";

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

    public static function convertColumnDateFormat(AdapterInterface $adapter, Model $table, $attrs=null,$timeAttrs=null)
    {
        $format = 'DD-MON-YYYY HH24:MI:SS';

        $temp = get_object_vars($table);
        if($attrs!=null){
        foreach ($attrs as $attr) {
            unset($temp[$attr]);
        }

        }

        if($timeAttrs!=null){
        foreach ($timeAttrs as $attr){
            unset($temp[$attr]);
        }

        }

        unset($temp['mappings']);

        if($timeAttrs!=null) {
            foreach ($timeAttrs as $attr) {
                unset($temp[$attr]);
            }
        }

        $attributes = array_keys($temp);

        $tempCols = [];

        if($attrs!=null) {
            foreach ($attrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT));
            }
        }

        if($timeAttrs!=null) {
            foreach ($timeAttrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIME_FORMAT));
            }
        }

        if($timeAttrs!=null){
        foreach ($timeAttrs as $attr) {
            array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIME_FORMAT));
        }
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

    public static function getExpressionDate($dateStr)
    {
        $format = Helper::ORACLE_DATE_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', '{$format}')");
    }

    public static function getExpressionTime($dateStr)
    {
        $format = Helper::ORACLE_TIME_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', '{$format}')");
    }

    public static function getcurrentExpressionDate()
    {
        $currentDate = date(self::PHP_DATE_FORMAT);
        return self::getExpressionDate($currentDate);
    }

    public static function hydrate($class, ResultSet $resultSet)
    {
        $tempArray = [];
        foreach ($resultSet as $item) {
            $model = new $class();
            $model->exchangeArrayFromDB($item->getArrayCopy());
            array_push($tempArray, $model);
        }
        return $tempArray;
    }

    public static function renderCustomView()
    {
        return function ($object) {
            $elems = $object->getValueOptions();
            $counter = 1;
            $name = $object->getName();
            foreach ($elems as $key => $value) {
                $temp = '';
                if ($object->getValue() == "") {
                    if ($counter == $object->getCheckedValue()) {
                        $temp = 'checked=checked';
                    }
                } else {
                    if ($object->getValue() == $key) {
                        $temp = 'checked=checked';
                    }
                }

                echo "<div class='md-radio'>";
               echo "<input $temp  type='radio' value='$key' name='$name' id='$name+$value' class='md-radiobtn'>";

               echo "<label for='$name+$value'>
                    <span></span>
                    <span class='check'></span>
                    <span class='box'></span> $value 
                </label>";
                echo "</div>";
                $counter++;
            }

        };
    }

    public static function generateUniqueName()
    {
        $date = new \DateTime();
        $t = $date->getTimestamp();
        return $t + rand(0, 1000);

    }
}