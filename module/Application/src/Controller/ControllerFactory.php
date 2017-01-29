<?php

namespace Application\Controller;

use Application\Factory\HrLogger;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $adapter = $container->get(AdapterInterface::class);
        $logger = $container->get(HrLogger::class);
        $controller = new $requestedName($adapter, $logger);
        return $controller;
    }

}
