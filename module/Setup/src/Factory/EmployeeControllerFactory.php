<?php
namespace Setup\Factory;
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 5:13 PM
 */
use Zend\ServiceManager\Factory\FactoryInterface;
use Setup\Controller\EmployeeController;
use Zend\Db\Adapter\AdapterInterface;

class EmployeeControllerFactory implements FactoryInterface{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        return new EmployeeController($container->get(AdapterInterface::class));
    }
}