<?php

namespace Report;

use Application\Controller\ControllerFactory;
use Report\Controller\ReportMonthlyController;
use Zend\Router\Http\Segment;

return[
    
    'router' => [
        'routes' => [
            'reportMonthly' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/report/monthly[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => ReportMonthlyController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
        ],
    ],
    
    
    
    'controllers' => [
        'factories' => [
            ReportMonthlyController::class => ControllerFactory::class,
        ],
    ],
    
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

