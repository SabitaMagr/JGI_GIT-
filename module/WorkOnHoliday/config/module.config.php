<?php
namespace WorkOnHoliday;

use Application\Controller\ControllerFactory;
use WorkOnHoliday\Controller\Status;
use WorkOnHoliday\Controller\Apply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'status' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnHoliday/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Status::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'apply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/workOnHoliday/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Apply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'status' => [
                [
                'label' => "Work on Holiday Request",
                'route' => "status"
            ],
                [
                'label' => "Work on Holiday Request",
                'route' => "status",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'status',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'status',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'status',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'apply' => [
                [
                'label' => "Work on Holiday Request",
                'route' => "apply"
            ],
                [
                'label' => "Work on Holiday Request",
                'route' => "apply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'apply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'apply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'apply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\Status::class => ControllerFactory::class,
            Controller\Apply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
