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
                        'action' => 'index',
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
                        'action' => 'index',
                    ]
                ],
            ],
            'department' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/department[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\DepartmentController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'position' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/position[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\PositionController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'serviceType'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/serviceType[/:action[/:id]]',
                    'constants'=>[
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\ServiceTypeController::class,
                        'action'=>'index',
                    ]
                ],
            ],

            'leaveType'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/leaveType[/:action[/:id]]',
                    'constant'=>[
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\LeaveTypeController::class,
                        'action'=>'index',
                    ]
                ],
            ],

            'shift'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/shift[/:action[/:id]]',
                    'constant'=>[
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\ShiftController::class,
                        'action'=>'index',
                    ]
                ],
            ],


            'jobHistory'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/jobHistory[/:action[/:id]]',
                    'constant'=>[
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\JobHistoryController::class,
                        'action'=>'index',
                    ]
                ],
            ],

            'empCurrentPosting'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/empCurrentPosting[/:action[/:id]]',
                    'constant'=>[
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>Controller\EmpCurrentPostingController::class,
                        'action'=>'index',
                    ]
                ],
            ],


        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\EmployeeController::class => Controller\ControllerFactory::class,
            Controller\DesignationController::class => Controller\ControllerFactory::class,
            Controller\CompanyController::class => Controller\ControllerFactory::class,
            Controller\BranchController::class => Controller\ControllerFactory::class,
            Controller\DepartmentController::class => Controller\ControllerFactory::class,
            Controller\PositionController::class => Controller\ControllerFactory::class,
            Controller\ServiceTypeController::class => Controller\ControllerFactory::class,
            Controller\LeaveTypeController::class=>Controller\ControllerFactory::class,
            Controller\ShiftController::class=>Controller\ControllerFactory::class,
            Controller\EmpCurrentPostingController::class=>Controller\ControllerFactory::class,
            Controller\JobHistoryController::class=>Controller\ControllerFactory::class,
        ]
    ],

  

     // Doctrine config
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity' ]
             ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                 ]
            ]
         ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];

