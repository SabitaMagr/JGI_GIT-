<?php

namespace Setup;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;

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
//                        Controller\EmployeeController::class => Factory\EmployeeControllerFactory::class
//                    ]
//                ],
//            ],
//        ];
//    }
}