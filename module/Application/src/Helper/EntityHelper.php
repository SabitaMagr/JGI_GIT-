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
use Zend\Db\Sql\Sql;

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

    public static function getColumnsList(AdapterInterface $adapter,$holidayId,$key, array $values,$concatWith=null){

        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from(['HB'=> 'HR_HOLIDAY_BRANCH'])
            ->join(['B' => "HR_BRANCHES"], 'HB.BRANCH_ID=B.BRANCH_ID', ['BRANCH_NAME']);

        $select->where(["HB.HOLIDAY_ID"=>$holidayId]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();

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