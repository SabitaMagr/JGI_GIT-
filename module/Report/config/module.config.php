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
                    'route' => '/report[/:action[/:id1[/:id2]]]',
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
                        'action' => 'departmentAll',
                    ],
                    [
                        'label' => 'Department|Months',
                        'route' => 'allreport',
                        'action' => 'departmentWise',
                    ],
                    [
                        'label' => 'Department|Month',
                        'route' => 'allreport',
                        'action' => 'departmentWiseDaily',
                    ],
                    [
                        'label' => 'Employee|Months',
                        'route' => 'allreport',
                        'action' => 'employeeWise',
                    ],
                    [
                        'label' => 'With Overtime',
                        'route' => 'allreport',
                        'action' => 'withOvertime',
                    ],
                    [
                        'label' => 'Leave Report',
                        'route' => 'allreport',
                        'action' => 'leaveReport',
                    ],
                    [
                        'label' => 'Hire & Fire',
                        'route' => 'allreport',
                        'action' => 'HireAndFireReport',
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

