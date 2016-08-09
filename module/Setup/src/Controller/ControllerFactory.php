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
        $controller=new $requestedName($adapter) ;
        return $controller;
    }
}