<?php

namespace Payroll;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'monthlyValue' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/payroll/monthlyValue[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\MonthlyValue::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'flatValue' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/payroll/flatValue[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\FlatValue::class,
                        'action' => 'index'
                    ]
                ]
            ]
        ]
    ],
    'navigation' => [
        'monthlyValue' => [
            [
                'label' => 'Monthly Value',
                'route' => 'monthlyValue',
            ],
            [
                'label' => 'Monthly Value',
                'route' => 'monthlyValue',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'monthlyValue',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'monthlyValue',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'monthlyValue',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'monthlyValue',
                        'action' => 'detail',
                    ],
                ]
            ]
        ],
        'flatValue' => [
            [
                'label' => 'Flat Value',
                'route' => 'flatValue',
            ],
            [
                'label' => 'Flat Value',
                'route' => 'flatValue',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'flatValue',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'flatValue',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'flatValue',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'flatValue',
                        'action' => 'detail',
                    ],
                ]
            ]
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\MonthlyValue::class => ControllerFactory::class,
            Controller\FlatValue::class => ControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


