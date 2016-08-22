<?php

namespace Setup\Helper;

<<<<<<< HEAD

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
        ]
    ];

    const HR_BLOOD_GROUPS='HR_BLOOD_GROUPS';
    const HR_DEPARTMENTS="HR_DEPARTMENTS";
    const HR_DESIGNATIONS="HR_DESIGNATIONS";
    const HR_DISTRICTS="HR_DISTRICTS";
    const HR_POSITIONS="HR_POSITIONS";
    const HR_GENDERS="HR_GENDERS";
    const HR_BRANCHES="HR_BRANCHES";
    const HR_VDC_MUNICIPALITY="HR_VDC_MUNICIPALITIY";
    const HR_SERVICE_TYPES="HR_SERVICE_TYPES";
    const HR_ZONES="HR_ZONES";
    const HR_RELIGIONS="HR_RELIGIONS";
    const HR_COMPANY="HR_COMPANY";


    public static function getTableKVList(AdapterInterface $adapter,$tableName){
        $gateway = new TableGateway($tableName, $adapter);
        $key=array_keys(self::$tablesAttributes[$tableName])[0];
        $value=array_values(self::$tablesAttributes[$tableName])[0];

        $resultset = $gateway->select();
=======
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\DepartmentRepository;
use Setup\Model\DesignationRepository;
use Setup\Model\PositionRepository;
use Setup\Model\ServiceTypeRepository;
use Setup\Model\BranchRepository;

class EntityHelper
{

    public static function getDepartmentKVList(AdapterInterface $adapter,$departmentId=null)
    {
        $repository = new DepartmentRepository($adapter);
        $entities = $repository->fetchActiveRecord();
        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            $entityResultSet = $entity->getArrayCopy();
            if($entityResultSet['DEPARTMENT_ID']!=$departmentId){
                $entitiesArray[$entityResultSet['DEPARTMENT_ID']]=$entityResultSet['DEPARTMENT_NAME'];
            }
        }
        return $entitiesArray;
    }

    public static function getBloodGroupKVList(EntityManager $em)
    {
        $repo = $em->getRepository(BloodGroups::class);
        $entities = $repo->findAll();

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entitiesArray[$entity->getBloodGroupId()] = $entity->getBloodGroupCode();
        }
        return $entitiesArray;
    }


    public static function getDesignationKVList(AdapterInterface $adapter,$designationId=null){
        
        $repository = new DesignationRepository($adapter);
        $entities = $repository->fetchActiveRecord();
        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            $entityResultSet = $entity->getArrayCopy();
            if($entityResultSet['DESIGNATION_ID']!=$designationId){
                $entitiesArray[$entityResultSet['DESIGNATION_ID']]=$entityResultSet['DESIGNATION_TITLE'];
            }
        }
        return $entitiesArray;
    }

    public static function getDistrictKVList(EntityManager $em)
    {
        $repo = $em->getRepository(HrDistricts::class);
        $entities = $repo->findAll();

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entitiesArray[$entity->getDistrictId()] = $entity->getDistrictName();

        }
        return $entitiesArray;
    }


    public static function getPositionKVList(AdapterInterface $adapter,$positionId=null){

        $repository = new PositionRepository($adapter);
        $entities = $repository->fetchActiveRecord();
        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            $entityResultSet = $entity->getArrayCopy();
            if($entityResultSet['POSITION_ID']!=$positionId){
                $entitiesArray[$entityResultSet['POSITION_ID']]=$entityResultSet['POSITION_NAME'];
            }
        }
        return $entitiesArray;
    }

    public static function getGenderKVList(EntityManager $em)
    {
        $repo = $em->getRepository(HrGenders::class);
        $entities = $repo->findAll();
>>>>>>> 9a4097faf254a9588174dd5164cb5398eeb31d96

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result[$key]] = $result[$value];
        }
        return $entitiesArray;
    }


<<<<<<< HEAD

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
=======
    public static function getBranchKVList(AdapterInterface $adapter,$branchId=null){
        $repository = new BranchRepository($adapter);
        $entities = $repository->fetchActiveRecord();
        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            $entityResultSet = $entity->getArrayCopy();
            if($entityResultSet['BRANCH_ID']!=$branchId){
                $entitiesArray[$entityResultSet['BRANCH_ID']]=$entityResultSet['BRANCH_NAME'];
            }
        }
        return $entitiesArray;
    }

    public static function getVdcMunicipalityKVList(EntityManager $em)
    {
        $repo = $em->getRepository(HrVdcMunicipality::class);
        $entities = $repo->findAll();

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entitiesArray[$entity->getVdcMunicipalityId()] = $entity->getVdcMunicipalityName();

        }
        return $entitiesArray;
    }


    public static function getServiceTypeKVList(AdapterInterface $adapter,$serviceTypeId=null){
        $repository = new ServiceTypeRepository($adapter);
        $entities = $repository->fetchActiveRecord();
        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            $entityResultSet = $entity->getArrayCopy();
            if($entityResultSet['SERVICE_TYPE_ID']!=$serviceTypeId){
                $entitiesArray[$entityResultSet['SERVICE_TYPE_ID']]=$entityResultSet['SERVICE_TYPE_NAME'];
            }
        }
        return $entitiesArray;
    }

    public static function getZoneKVList(EntityManager $em)
    {
        $repo = $em->getRepository(HrZones::class);
        $entities = $repo->findAll();

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entitiesArray[$entity->getZoneId()] = $entity->getZoneName();

        }
        return $entitiesArray;
    }


    // public static function getEmployeeKVList(EntityManager $em,$employeeId=null){
    //     $repo = $em->getRepository(HrEmployees::class);
    //     $entities = $repo->findAll();

    //     $entitiesArray=array();
    //     $entitiesArray[0]='----';
    //     foreach($entities as $entity){
    //         if($entity->getEmployeeId()!=$employeeId){
    //             $entitiesArray[$entity->getEmployeeId()]=$entity->getEmployeeName();
    //         }
    //     }
    //     return $entitiesArray;
    // }


    public static function hydrate(EntityManager $entityManager, $class, $array)
    {
        $hydrator = new DoctrineHydrator($entityManager);
        return $hydrator->hydrate($array, new $class());
    }


    public static function extract(EntityManager $entityManager, $object)
    {
        $hydrator = new DoctrineHydrator($entityManager);
        return $hydrator->extract($object);
    }
>>>>>>> 9a4097faf254a9588174dd5164cb5398eeb31d96

}