<?php

namespace Application\Helper;

use Exception;
use ReflectionClass;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Gender;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Predicate;
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

    public static function getTableList(AdapterInterface $adapter, string $tableName, array $columnList, array $where = null, string $predicate = Predicate::OP_AND) {
        $gateway = new TableGateway($tableName, $adapter);
        $zendResult = $gateway->select(function(Select $select) use($columnList, $where, $predicate) {
            $select->columns($columnList, false);
            if ($where != null) {
                $select->where($where, $predicate);
            }
        });
        return Helper::extractDbData($zendResult, true);
    }

    public static function getColumnNameArrayWithOracleFns(string $requestedName, array $initCapColumnList = null, array $dateColumnList = null, array $timeColumnList = null, array $timeIntervalColumnList = null, array $otherColumnList = null, string $shortForm = null, $selectedOnly = false, $inStringForm = false, array $minuteToHourColumnList = null) {
        $refl = new ReflectionClass($requestedName);
        $table = $refl->newInstanceArgs();

        $objAttrs = array_keys(get_object_vars($table));
        $objCols = [];
<<<<<<< HEAD
=======
        if (HrEmployees::class == $requestedName) {
            $pre = "";
            if ($shortForm != null) {
                $pre = $shortForm.".";
            }
            $fullNameExpression = new Expression("FULL_NAME({$pre}FIRST_NAME,{$pre}MIDDLE_NAME,{$pre}LAST_NAME) AS FULL_NAME");
            array_push($objCols, $inStringForm ? $fullNameExpression->getExpression() : $fullNameExpression);
        }
>>>>>>> e7b1638cb289a11ed3129090edf57c8e2345afde

        foreach ($objAttrs as $objAttr) {
            if ('mappings' === $objAttr) {
                continue;
            }
            $tempCol = $table->mappings[$objAttr];
            if ($initCapColumnList !== null && in_array($tempCol, $initCapColumnList)) {
                $initCapExpression = Helper::columnExpression($tempCol, $shortForm, self::ORACLE_FUNCTION_INITCAP);
                array_push($objCols, $inStringForm ? $initCapExpression->getExpression() : $initCapExpression);
                continue;
            }

            if ($dateColumnList !== null && in_array($tempCol, $dateColumnList)) {
                $dateExpression = self::formatColumn($tempCol, $shortForm, Helper::ORACLE_DATE_FORMAT);
                array_push($objCols, $inStringForm ? $dateExpression->getExpression() : $dateExpression);
                continue;
            }
            if ($timeColumnList !== null && in_array($tempCol, $timeColumnList)) {
                $timeExpression = self::formatColumn($tempCol, $shortForm, Helper::ORACLE_TIME_FORMAT);
                array_push($objCols, $inStringForm ? $timeExpression->getExpression() : $timeExpression);
                continue;
            }
            if ($timeIntervalColumnList !== null && in_array($tempCol, $timeIntervalColumnList)) {
                $timeIntervalExpression = self::formatColumn($tempCol, $shortForm, Helper::ORACLE_TIMESTAMP_FORMAT);
                array_push($objCols, $inStringForm ? $timeIntervalExpression->getExpression() : $timeIntervalExpression);
                continue;
            }
            if ($otherColumnList !== null && in_array($tempCol, $otherColumnList)) {
                array_push($objCols, $tempCol);
                continue;
            }
            if ($minuteToHourColumnList != null && in_array($tempCol, $minuteToHourColumnList)) {
                $minuteToHour = self::minuteToHourColumn($tempCol, $shortForm);
                array_push($objCols, $inStringForm ? $minuteToHour->getExpression() : $minuteToHour);
                continue;
            }

            if (!$selectedOnly) {
                array_push($objCols, Helper::columnExpression($tempCol, $shortForm));
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

    public static function minuteToHourColumn($columnName, $shortForm = null) {
        $pre = "";
        if ($shortForm != null && sizeof($shortForm) != 0) {
            $pre = $shortForm . ".";
        }
        return "NVL2({$pre}{$columnName},LPAD(TRUNC({$pre}{$columnName}/60,0),2, 0)||':'||LPAD(MOD({$pre}{$columnName},60),2, 0),null) AS {$columnName}";
    }

    public static function getSearchData($adapter) {
        /* search values */
        $companyList = self::getTableList($adapter, Company::TABLE_NAME, [Company::COMPANY_ID, Company::COMPANY_NAME], [Company::STATUS => "E"]);
        $branchList = self::getTableList($adapter, Branch::TABLE_NAME, [Branch::BRANCH_ID, Branch::BRANCH_NAME, Branch::COMPANY_ID], [Branch::STATUS => "E"]);
        $departmentList = self::getTableList($adapter, Department::TABLE_NAME, [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME, Department::COMPANY_ID, Department::BRANCH_ID], [Department::STATUS => "E"]);
        $designationList = self::getTableList($adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE, Designation::COMPANY_ID], [Designation::STATUS => 'E']);
        $positionList = self::getTableList($adapter, Position::TABLE_NAME, [Position::POSITION_ID, Position::POSITION_NAME, Position::COMPANY_ID], [Position::STATUS => "E"]);
        $serviceTypeList = self::getTableList($adapter, ServiceType::TABLE_NAME, [ServiceType::SERVICE_TYPE_ID, ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => "E"]);
        $serviceEventTypeList = self::getTableList($adapter, ServiceEventType::TABLE_NAME, [ServiceEventType::SERVICE_EVENT_TYPE_ID, ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => "E"]);
        $genderList = self::getTableList($adapter, Gender::TABLE_NAME, [Gender::GENDER_ID, Gender::GENDER_NAME], [Gender::STATUS => "E"]);
        $employeeList = self::getTableList($adapter, HrEmployees::TABLE_NAME, [
                    HrEmployees::EMPLOYEE_ID,
                    HrEmployees::FIRST_NAME,
                    HrEmployees::MIDDLE_NAME,
                    HrEmployees::LAST_NAME,
                    HrEmployees::COMPANY_ID,
                    HrEmployees::BRANCH_ID,
                    HrEmployees::DEPARTMENT_ID,
                    HrEmployees::DESIGNATION_ID,
                    HrEmployees::POSITION_ID,
                    HrEmployees::SERVICE_TYPE_ID,
                    HrEmployees::SERVICE_EVENT_TYPE_ID,
                    HrEmployees::GENDER_ID,
                        ], [HrEmployees::STATUS => "E"]);

        $searchValues = [
            'company' => $companyList,
            'branch' => $branchList,
            'department' => $departmentList,
            'designation' => $designationList,
            'position' => $positionList,
            'serviceType' => $serviceTypeList,
            'serviceEventType' => $serviceEventTypeList,
            'gender' => $genderList,
            'employee' => $employeeList
        ];
        /* end of search values */

        return $searchValues;
    }

    public static function batchSQL($fn) {
        self::rawQueryResult($this->adapter, "SAVEPOINT multipleEmployeeAssign");
        try {
            $fn();
        } catch (Exception $e) {
            self::rawQueryResult($this->adapter, "ROLLBACK TO SAVEPOINT multipleEmployeeAssign");
        }
        self::rawQueryResult($this->adapter, "COMMIT");
    }

}
