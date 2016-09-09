<?php

namespace Setup\Helper;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;


class EntityHelper
{
    public static $tablesAttributes=[
        self::HR_BLOOD_GROUPS=>[
            "BLOOD_GROUP_ID"=>"BLOOD_GROUP_CODE"
        ],
        self::HR_DEPARTMENTS=>[
            "DEPARTMENT_ID"=>"DEPARTMENT_NAME"
        ],
        self::HR_DESIGNATIONS=>[
            "DESIGNATION_ID"=>"DESIGNATION_TITLE"
        ],
        self::HR_DISTRICTS=>[
            "DISTRICT_ID"=>"DISTRICT_NAME"
        ],
        self::HR_POSITIONS=>[
            "POSITION_ID"=>"POSITION_NAME"
        ],
        self::HR_GENDERS=>[
            "GENDER_ID"=>"GENDER_NAME"
        ],
        self::HR_BRANCHES=>[
            "BRANCH_ID"=>"BRANCH_NAME"
        ],
        self::HR_VDC_MUNICIPALITY=>[
            "VDC_MUNICIPALITY_ID"=>"VDC_MUNICIPALITY_NAME"
        ],
        self::HR_SERVICE_TYPES=>[
            "SERVICE_TYPE_ID"=>"SERVICE_TYPE_NAME"
        ],
        self::HR_ZONES=>[
            "ZONE_ID"=>"ZONE_NAME"
        ],self::HR_RELIGIONS=>[
            "RELIGION_ID"=>"RELIGION_NAME"
        ],
        self::HR_COMPANY=>[
            "COMPANY_ID"=>"COMPANY_NAME"
        ],
        self::HR_EMPLOYEES=>[
            'EMPLOYEE_ID'=>'FIRST_NAME'
        ],
        self::HR_COUNTRIES=>[
            'COUNTRY_ID'=>'COUNTRY_NAME'
        ],
        self::HR_FILE_TYPE=>[
            'FILETYPE_CODE'=>'NAME'
        ]
    ];

    const HR_BLOOD_GROUPS='HR_BLOOD_GROUPS';
    const HR_DEPARTMENTS="HR_DEPARTMENTS";
    const HR_DESIGNATIONS="HR_DESIGNATIONS";
    const HR_DISTRICTS="HR_DISTRICTS";
    const HR_POSITIONS="HR_POSITIONS";
    const HR_GENDERS="HR_GENDERS";
    const HR_BRANCHES="HR_BRANCHES";
    const HR_VDC_MUNICIPALITY="HR_VDC_MUNICIPALITIES";
    const HR_SERVICE_TYPES="HR_SERVICE_TYPES";
    const HR_ZONES="HR_ZONES";
    const HR_RELIGIONS="HR_RELIGIONS";
    const HR_COMPANY="HR_COMPANY";
    const HR_EMPLOYEES='HR_EMPLOYEES';
    const HR_COUNTRIES='HR_COUNTRIES';
    const HR_FILE_TYPE='HR_FILE_TYPE';


    public static function getTableKVList(AdapterInterface $adapter,$tableName,$id=null){
        $gateway = new TableGateway($tableName, $adapter);
        $key=array_keys(self::$tablesAttributes[$tableName])[0];
        $value=array_values(self::$tablesAttributes[$tableName])[0];

        if($id==null){
        $resultset = $gateway->select();
        }else{
        $resultset = $gateway->select($id);

        }

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result[$key]] = $result[$value];
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