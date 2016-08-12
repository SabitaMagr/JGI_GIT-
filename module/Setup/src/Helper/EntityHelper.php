<?php

namespace Setup\Helper;

use Doctrine\ORM\EntityManager;
use Setup\Entity\BloodGroups;
use Setup\Entity\HrDistricts;
use Setup\Entity\HrGenders;
use Setup\Entity\HrVdcMunicipality;
use Setup\Entity\HrZones;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class EntityHelper
{
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