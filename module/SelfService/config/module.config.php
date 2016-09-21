<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/15/16
 * Time: 12:55 PM
 */
namespace SelfService;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'myattendance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/myattendance[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\MyAttendance::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'navigation' => [
        'myattendance' => [
            [
                'label' => 'Attendance',
                'route' => 'myattendance',
            ],
            [
                'label' => 'Attendance',
                'route' => 'myattendance',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'myattendance',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Entry',
                        'route' => 'myattendance',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'myattendance',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\MyAttendance::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];