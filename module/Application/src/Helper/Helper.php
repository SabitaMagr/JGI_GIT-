<?php

namespace Application\Helper;

use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class Helper {

    const ORACLE_DATE_FORMAT = "DD-MON-YYYY";
    const ORACLE_TIME_FORMAT = "HH:MI AM";
    const MYSQL_DATE_FORMAT = "";
    const PHP_DATE_FORMAT = "d-M-Y";
    const PHP_TIME_FORMAT = "h:i A";
    const FLOAT_ROUNDING_DIGIT_NO = 2;
    const UPLOAD_DIR = __DIR__ . "/../../../../public/uploads";
    const SH_DIR = __DIR__ . "/../../../../public/sh";

//  method to add flashmessage to view
    public static function addFlashMessagesToArray($context, $return) {
        $flashMessenger = $context->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return['messages'] = $flashMessenger->getMessages();
        }
        return $return;
    }

//  method to generate maxId
    public static function getMaxId(AdapterInterface $adapter, $tableName, $columnName) {
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($tableName);
        $select->columns(["MAX({$columnName}) AS MAX_{$columnName}"], false);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $row = $result->current();
        return $row["MAX_{$columnName}"];
    }

    public static function convertColumnDateFormat(AdapterInterface $adapter, Model $table, $attrs = null, $timeAttrs = null, $shortForm = null) {
        $format = 'DD-MON-YYYY HH24:MI:SS';

        $temp = get_object_vars($table);
        if ($attrs != null) {
            foreach ($attrs as $attr) {
                unset($temp[$attr]);
            }
        }

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                unset($temp[$attr]);
            }
        }

        unset($temp['mappings']);

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                unset($temp[$attr]);
            }
        }

        $attributes = array_keys($temp);

        $tempCols = [];

        if ($attrs != null) {
            foreach ($attrs as $attr) {
//                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT));
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_DATE_FORMAT, $shortForm));
            }
        }

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIME_FORMAT, $shortForm));
            }
        }

        if ($timeAttrs != null) {
            foreach ($timeAttrs as $attr) {
                array_push($tempCols, Helper::appendDateFormat($adapter, $table->mappings[$attr], self::ORACLE_TIME_FORMAT, $shortForm));
            }
        }

        foreach ($attributes as $attribute) {
            array_push($tempCols, Helper::columnExpression($table->mappings[$attribute], $shortForm, null));
        }
        return $tempCols;
    }

    public static function appendDateFormat($adapter, $columnName, $format, $shortForm = null) {
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "";
        switch (strtolower($adapter->getPlatform()->getName())) {
            case strtolower("Oracle"):
                $tempStr = "TO_CHAR({$pre}{$columnName}, '{$format}') AS {$columnName}";
                break;

            case strtolower("Mysql"):

                break;
        }
        return $tempStr;
    }

    public static function dateExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_DATE_FORMAT;
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "TO_CHAR({$pre}{$columnName}, '{$format}') AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function timeExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_TIME_FORMAT;
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "TO_CHAR({$pre}{$columnName}, '{$format}') AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function datetimeExpression($columnName, $shortForm = null) {
        $format = Helper::ORACLE_DATE_FORMAT . " " . self::ORACLE_TIME_FORMAT;
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        $tempStr = "TO_CHAR({$pre}{$columnName}, '{$format}') AS {$columnName}";
        return new Expression($tempStr);
    }

    public static function columnExpression($columnName, $shortForm = null, $function = null, $customColumnName = null) {
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }

        if ($customColumnName == null) {
            $customColumnName = $columnName;
        }
        if ($function == NULL) {
            $tempStr = "{$pre}{$columnName} AS {$columnName}";
        } else {
            $tempStr = "${function}({$pre}{$columnName}) AS {$customColumnName}";
        }
        return new Expression($tempStr);
    }

    public static function getExpressionDate($dateStr) {
        $format = Helper::ORACLE_DATE_FORMAT;
        return new Expression("TO_DATE('{$dateStr}', '{$format}')"
        );
    }

    public static function getExpressionTime($dateStr, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_TIME_FORMAT;
        }
        return new Expression("TO_DATE('{$dateStr}', '{$format}')");
    }

    public static function getExpressionDateTime($dateStr, $format = null) {
        if ($format == null) {
            $format = Helper::ORACLE_DATE_FORMAT . " " . Helper::ORACLE_TIME_FORMAT;
        }
        return new Expression("TO_DATE('{$dateStr}', '{$format}')");
    }

    public static function getcurrentExpressionDate() {
        $currentDate = date(self::PHP_DATE_FORMAT);
        return self::getExpressionDate($currentDate);
    }
    public static function getcurrentExpressionTime(){
        $currentTime = date(self::PHP_TIME_FORMAT);
        return self::getExpressionTime($currentTime);
    }

    public static function getcurrentExpressionDateTime() {
        $currentDate = date(self::PHP_DATE_FORMAT . " " . self::PHP_TIME_FORMAT);
        return self::getExpressionDateTime($currentDate);
    }

    public static function getCurrentDate() {
        $currentDate = date(self::PHP_DATE_FORMAT);
        return $currentDate;
    }

    public static function getcurrentMonthDayExpression() {
        $currentDate = date('d-M');
        $format = 'DD-MON';
        return new Expression("TO_DATE('{$currentDate}','{$format}')");
    }

    public static function hydrate($class, ResultSet $resultSet) {
        $tempArray = [];
        foreach ($resultSet as $item) {
            $model = new $class();
            $model->exchangeArrayFromDB(
                    $item->getArrayCopy());
            array_push($tempArray, $model);
        }
        return $tempArray;
    }

    public static function renderCustomView() {
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

                echo "<div class = 'md-radio'>";
                echo "<input $temp type = 'radio' value = '$key' name = '$name' id = '$name+$value' class = 'md-radiobtn radioButton'>";

                echo "<label for = '$name+$value'>
                <span></span>
                <span class = 'check'></span>
                <span class = 'box'></span> $value
                </label>";
                echo "</div>";
                $counter++;
            }
        };
    }

    public static function generateUniqueName() {
        $date = new \DateTime();
        $t = $date->getTimestamp();
        return $t + rand(0, 1000);
    }

    public static function extractDbData($rawArray, bool $inArray = false, string $arrangeWithKey = null): array {
        $extractedArray = [];
        foreach ($rawArray as $item) {
            if ($inArray) {
                $item = $item->getArrayCopy();
            }
            if ($arrangeWithKey == null) {
                array_push($extractedArray, $item);
            } else {
                $extractedArray[$item[$arrangeWithKey]] = $item;
            }
        }
        return $extractedArray;
    }

    public static function maintainFloatNumberFormat($floatNumber) {
        return number_format($floatNumber, self::FLOAT_ROUNDING_DIGIT_NO, '.', '');
    }
}
