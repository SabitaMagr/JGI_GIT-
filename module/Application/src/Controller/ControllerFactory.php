<?php

namespace Application\Controller;

use Application\Factory\HrLogger;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Log\Logger;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $getDependency = function(string $className, ContainerInterface $container) {
            $output = null;
            switch ($className) {
                case AdapterInterface::class:
                    $output = $container->get(AdapterInterface::class);
                    break;
                case Logger::class:
                    $output = $container->get(HrLogger::class);
                    break;
                case ContainerInterface::class:
                    $output = $container;
                    break;
                case \Application\Factory\ConfigInterface::class:
                    $appConfig = new \Application\Factory\ApplicationConfig();
                    $appConfig->setApplicationConfig($container->get('config'));
                    $output = $appConfig;
                    break;
            }
            return $output;
        };
        $refl = new ReflectionClass($requestedName);
        $params = $refl->getConstructor()->getParameters();
        $initParams = [];
        foreach ($params as $key => $param) {
            $initParams[$key] = $getDependency($param->getClass()->name, $container);
        }
        return $refl->newInstanceArgs($initParams);
//        $adapter = $container->get(AdapterInterface::class);
//        $logger = $container->get(HrLogger::class);
//        $controller = new $requestedName($adapter, $logger);
//        return $controller;
    }

}
