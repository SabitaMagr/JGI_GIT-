<?php

namespace Setup\Helper;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;


class EntityHelper
{
    public static $tablesAttributes = [
        self::HRIS_BLOOD_GROUPS => [
            "BLOOD_GROUP_ID" => "BLOOD_GROUP_CODE"
        ],
        self::HRIS_DEPARTMENTS => [
            "DEPARTMENT_ID" => "DEPARTMENT_NAME"
        ],
        self::HRIS_DESIGNATIONS => [
            "DESIGNATION_ID" => "DESIGNATION_TITLE"
        ],
        self::HRIS_DISTRICTS => [
            "DISTRICT_ID" => "DISTRICT_NAME"
        ],
        self::HRIS_POSITIONS => [
            "POSITION_ID" => "POSITION_NAME"
        ],
        self::HRIS_GENDERS => [
            "GENDER_ID" => "GENDER_NAME"
        ],
        self::HRIS_BRANCHES => [
            "BRANCH_ID" => "BRANCH_NAME"
        ],
        self::HRIS_VDC_MUNICIPALITY => [
            "VDC_MUNICIPALITY_ID" => "VDC_MUNICIPALITY_NAME"
        ],
        self::HRIS_SERVICE_TYPES => [
            "SERVICE_TYPE_ID" => "SERVICE_TYPE_NAME"
        ],
        self::HRIS_ZONES => [
            "ZONE_ID" => "ZONE_NAME"
        ], self::HRIS_RELIGIONS => [
            "RELIGION_ID" => "RELIGION_NAME"
        ],
        self::HRIS_COMPANY => [
            "COMPANY_ID" => "COMPANY_NAME"
        ],
        self::HRIS_EMPLOYEES => [
            'EMPLOYEE_ID' => 'FIRST_NAME'
        ],
        self::HRIS_COUNTRIES => [
            'COUNTRY_ID' => 'COUNTRY_NAME'
        ],
        self::HRIS_FILE_TYPE => [
            'FILETYPE_CODE' => 'NAME'
        ]
    ];

    const HRIS_BLOOD_GROUPS = 'HRIS_BLOOD_GROUPS';
    const HRIS_DEPARTMENTS = "HRIS_DEPARTMENTS";
    const HRIS_DESIGNATIONS = "HRIS_DESIGNATIONS";
    const HRIS_DISTRICTS = "HRIS_DISTRICTS";
    const HRIS_POSITIONS = "HRIS_POSITIONS";
    const HRIS_GENDERS = "HRIS_GENDERS";
    const HRIS_BRANCHES = "HRIS_BRANCHES";
    const HRIS_VDC_MUNICIPALITY = "HRIS_VDC_MUNICIPALITIES";
    const HRIS_SERVICE_TYPES = "HRIS_SERVICE_TYPES";
    const HRIS_ZONES = "HRIS_ZONES";
    const HRIS_RELIGIONS = "HRIS_RELIGIONS";
    const HRIS_COMPANY = "HRIS_COMPANY";
    const HRIS_EMPLOYEES = 'HRIS_EMPLOYEES';
    const HRIS_COUNTRIES = 'HRIS_COUNTRIES';
    const HRIS_FILE_TYPE = 'HRIS_FILE_TYPE';
    const HRIS_FISCAL_YEARS="HRIS_FISCAL_YEARS";


    public static function getTableKVList(AdapterInterface $adapter, $tableName, $id = null)
    {
        $gateway = new TableGateway($tableName, $adapter);
        $key = array_keys(self::$tablesAttributes[$tableName])[0];
        $value = array_values(self::$tablesAttributes[$tableName])[0];

        if ($id == null) {
            $resultset = $gateway->select();
        } else {
            $resultset = $gateway->select($id);
        }

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result[$key]] = $result[$value];
        }
        if($tableName==self::HRIS_GENDERS){
            $entitiesArray[-1]='All';
        }
        return $entitiesArray;
    }



//    public static function hydrate(EntityManager $entityManager, $class, $array)
//    {
//        $hydrator = new DoctrineHydrator($entityManager);
//        return $hydrator->hydrate($array, new $class());
//    }
//
//
//    public static function extract(EntityManager $entityManager, $object)
//    {
//        $hydrator = new DoctrineHydrator($entityManager);
//        return $hydrator->extract($object);
//    }

}