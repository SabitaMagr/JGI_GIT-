<?php
namespace ServiceQuestion;

use Application\Controller\ControllerFactory;
use Overtime\Controller\OvertimeApply;
use Overtime\Controller\OvertimeStatus;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'overtimeStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/overtime/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OvertimeStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'overtimeApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/overtime/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => OvertimeApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'overtimeStatus' => [
                [
                'label' => "Overtime Request",
                'route' => "overtimeStatus"
            ],
                [
                'label' => "Overtime Request",
                'route' => "overtimeStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'overtimeStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'overtimeStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'overtimeStatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'overtimeApply' => [
                [
                'label' => "Overtime Apply",
                'route' => "overtimeApply"
            ],
                [
                'label' => "Overtime Apply",
                'route' => "overtimeApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'overtimeApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'overtimeApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'overtimeApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\OvertimeStatus::class => ControllerFactory::class,
            Controller\OvertimeApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
