<?php
namespace Setup\Controller;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class ControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        // $adapter=$container->get(AdapterInterface::class);
       // $conn = [
       //     'host' =>'localhost',
       //     'user' => 'root',
       //     'password' => 'root',
       //     'dbname' =>'album' ,
       //     'driver'=>'mysqli'
       // ];

        $conn = [
            'host' =>'192.168.4.2',
            'user' => 'HRIS',
            'password' => 'NEO_HRIS',
            'servicename' =>'ITN' ,
            'driver'=>'oci8'
        ];

        $paths = array(__DIR__ . "/../Entity/");
        $isDevMode = false;

        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);

        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($conn, $config);
        $controller = new $requestedName($entityManager);

        return $controller;
    }
}