<?php

namespace Setup;

use SebastianBergmann\Comparator\Factory;
use Setup\Controller\EmployeeController;
use Setup\Model\EmployeeRepository;
use Zend\Db\TableGateway\TableGateway;
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

            'company' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/company[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CompanyController::class,
                        'action' => 'add',
                    ],
                ],
            ],
            'branch' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/branch[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\BranchController::class,
                        'action' => 'add',
                    ]
                ],
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
    'controllers' => [
        'factories' => [
            Controller\EmployeeController::class => Controller\EmployeeControllerFactory::class,
            Controller\DesignationController::class => Controller\DesignationControllerFactory::class,
            Controller\CompanyController::class => InvokableFactory::class,
            Controller\BranchController::class => InvokableFactory::class,
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];