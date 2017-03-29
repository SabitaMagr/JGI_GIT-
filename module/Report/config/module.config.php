<?php

namespace Report;

use Application\Controller\ControllerFactory;
use Report\Controller\AllReportController;
use Zend\Router\Http\Segment;

return[
    
    'router' => [
        'routes' => [
            'allreport' => [
                'type' => Segment::class,
                'options' => [
                    'route' => 'report/allreport[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AllReportController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
        ],
    ],
    
    
    
    'controllers' => [
        'factories' => [
            AllReportController::class => ControllerFactory::class,
        ],
    ],
    
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

