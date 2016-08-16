<?php

namespace Setup\Helper;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Setup\Entity\HrDepartments;
use Setup\Entity\HrDesignations;
use Setup\Entity\HrBranches;
use Setup\Entity\HrPositions;
use Setup\Entity\HrServiceTypes;
//use Setup\Entity\HrEmployees;

class EntityHelper
{
    public static function getDepartmentKVList(EntityManager $em,$departmentId=null)
    {
        $repo = $em->getRepository(HrDepartments::class);
        $entities = $repo->findAll();

        $entitiesArray=array();
        $entitiesArray[0]='----';
        foreach($entities as $entity){
            if($entity->getDepartmentId()!=$departmentId){
                $entitiesArray[$entity->getDepartmentId()]=$entity->getDepartmentName();
            }
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
       // print_r($array); die();
        return $hydrator->hydrate($array, new $class());
    }
    
    public static function extract(EntityManager $entityManager, $object)
    {
        $hydrator = new DoctrineHydrator($entityManager);
        return $hydrator->extract($object);
    }
}