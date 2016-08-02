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
            
            'company' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/company[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CompanyController::class,
                        'action'     => 'add',
                    ],
                ],
            ],
            'branch'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/branch[/:action[/:id]]',
                    'constraints'=>[
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+', 
                    ],
                    'defaults'=>[
                        'controller'=>Controller\BranchController::class,
                        'action'=>'add',
                    ]
                ],
            ],
            'department'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/department[/:action[/:id]]',
                    'constants'=>[
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\DepartmentController::class,
                        'action'=>'add',
                    ]
                ],
            ],
            'position'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/position[/:action[/:id]]',
                    'constants'=>[
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\PositionController::class,
                        'action'=>'add',
                    ]
                ],
            ],

            'employeeType'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/employeeType[/:action[/:id]]',
                    'constants'=>[
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\EmployeeTypeController::class,
                        'action'=>'add',
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
    'controllers'=>[
        'factories'=>[
            Controller\EmployeeController::class=>Factory\EmployeeControllerFactory::class,
            Controller\CompanyController::class => InvokableFactory::class,
            Controller\BranchController::class => InvokableFactory::class,
            Controller\DepartmentController::class => InvokableFactory::class,
            Controller\PositionController::class => InvokableFactory::class,
            Controller\EmployeeTypeController::class => InvokableFactory::class,
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];