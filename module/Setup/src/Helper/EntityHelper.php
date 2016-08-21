<?php

namespace Setup\Helper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\DepartmentRepository;

class EntityHelper
{

    public static function getDepartmentKVList(AdapterInterface $adapter,$departmentId=null)
    {
        $departmentRepository = new DepartmentRepository($adapter);
        $entities = $departmentRepository->fetchAll();
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


    public static function getDesignationKVList(EntityManager $em,$designationId=null){
        $repo = $em->getRepository(HrDesignations::class);
        $entities = $repo->findAll();

        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            if($entity->getDesignationId()!=$designationId){
                $entitiesArray[$entity->getDesignationId()]=$entity->getDesignationTitle();
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


    public static function getPositionKVList(EntityManager $em,$positionId=null){
        $repo = $em->getRepository(HrPositions::class);
        $entities = $repo->findAll();

        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            if($entity->getPositionId()!=$positionId){
                $entitiesArray[$entity->getPositionId()]=$entity->getPositionName();
            }
        }
        return $entitiesArray;
    }

    public static function getGenderKVList(EntityManager $em)
    {
        $repo = $em->getRepository(HrGenders::class);
        $entities = $repo->findAll();

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entitiesArray[$entity->getGenderId()] = $entity->getGenderName();

        }
        return $entitiesArray;
    }


    public static function getBranchKVList(EntityManager $em,$branchId=null){
        $repo = $em->getRepository(HrBranches::class);
        $entities = $repo->findAll();

        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            if($entity->getBranchId()!=$branchId){
                $entitiesArray[$entity->getBranchId()]=$entity->getBranchName();
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


    public static function getServiceTypeKVList(EntityManager $em,$serviceTypeId=null){
        $repo = $em->getRepository(HrServiceTypes::class);
        $entities = $repo->findAll();

        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            if($entity->getServiceTypeId()!=$serviceTypeId){
                $entitiesArray[$entity->getServiceTypeId()]=$entity->getServiceTypeName();
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

}