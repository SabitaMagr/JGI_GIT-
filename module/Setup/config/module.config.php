<?php

namespace Setup;

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
                        'controller'=>Controller\EmployeeController::class,
                        'action'=>'index'

                    ]
                ]

            ],
//            'edit'=>[
//                'type'=>Segment::class,
//                'options'=>[
//                    'route'=>'/setup/:id',
//                    'defaults'=>[
//                        'controller'=>Controller\EmployeeController::class,
//                        'action'=>'edit'
//                    ]
//                ]
//            ],
//            'list'=>[
//                'type'=>Segment::class,
//                'options'=>[
//                    'route'=>'/setup[/:action]',
//                    'defaults'=>[
//                        'controller'=>Controller\EmployeeController::class,
//                        'action'=>'list'
//                    ]
//                ]
//            ]
        ]
    ],
    'controllers'=>[
        'factories'=>[
            Controller\EmployeeController::class=>Factory\EmployeeControllerFactory::class
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];