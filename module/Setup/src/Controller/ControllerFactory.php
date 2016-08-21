<?php
namespace Setup\Controller;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter=$container->get(AdapterInterface::class);             
        
        // $conn = [
        //     'host' =>'192.168.4.2',
        //     'user' => 'HRIS',
        //     'password' => 'NEO_HRIS',
        //     'servicename' =>'ITN' ,
        //     'dbname' =>'ITN' ,
        //     'driver'=>'oci8'
        // ];

        // $paths = array(__DIR__ . "/../Entity/");
        // $isDevMode = false;

        // $config = Setup::createConfiguration($isDevMode);
        // $driver = new AnnotationDriver(new AnnotationReader(), $paths);

        // AnnotationRegistry::registerLoader('class_exists');
        // $config->setMetadataDriverImpl($driver);

        // $entityManager = EntityManager::create($conn, $config);

        
        $controller = new $requestedName($adapter);

        return $controller;


    }
}