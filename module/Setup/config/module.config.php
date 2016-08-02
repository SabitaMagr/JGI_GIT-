<?php

namespace Setup;

use SebastianBergmann\Comparator\Factory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Db\Adapter\AdapterInterface;

return [
    'router' => [
        'routes' => [
            'setup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/setup[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\EmployeeController::class,
                        'action' => 'index'

                    ]
                ]
            ],
            'designation' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/designation[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\DesignationController::class,
                        'action' => 'index'

                    ]
                ]
            ],
        ]
    ],


    'controllers' => [
        'factories' => [
            Controller\EmployeeController::class => Factory\EmployeeControllerFactory::class,
            Controller\DesignationController::class=>Factory\DesignationControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];