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
                    'route' => '/allreport[/:action[/:id1[/:id2]]]',
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
    'navigation' => [
        'allreport' => [
            [
                'label' => 'Asset Type',
                'route' => 'allreport',
            ], [
                'label' => 'Report',
                'route' => 'allreport',
                'pages' => [
                    [
                        'label' => 'Departments|Months',
                        'route' => 'allreport',
                        'action' => 'reportOne',
                    ],
                    [
                        'label' => 'Department|Months',
                        'route' => 'allreport',
                        'action' => 'reportTwo',
                    ],
                    [
                        'label' => 'Department|Month',
                        'route' => 'allreport',
                        'action' => 'reportThree',
                    ],
                    [
                        'label' => 'Employee|Months',
                        'route' => 'allreport',
                        'action' => 'reportFour',
                    ],
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

