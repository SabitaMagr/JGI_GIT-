<?php

namespace Setup;

use SebastianBergmann\Comparator\Factory;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }



//    public function getControllerConfig()
//    {
//        return [
//            'factories' => [
//                'controllers' => [
//                    'factories' => [
//                        Controller\EmployeeController::class => Factory\EmployeeControllerFactory::class,
//                        Controller\DesignationController::class=>Factory\DesignationControllerFactory::class
//                    ]
//                ],
//            ],
//        ];
//    }


    public function getServiceConfig()
    {
        return [
            'factories' => [
                Factory\EmployeeControllerFactory::class => InvokableFactory::class,
                Factory\DesignationControllerFactory::class => InvokableFactory::class
            ],
        ];
    }
}