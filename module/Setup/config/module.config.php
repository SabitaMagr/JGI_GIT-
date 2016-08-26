<?php

namespace Setup;

use Setup\Model\EmployeeRepository;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'employee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/employee[/:action[/:id]]',
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

            'serviceType' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/serviceType[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ServiceTypeController::class,
                        'action' => 'index',
                    ]
                ],
            ],


            'jobHistory' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/jobHistory[/:action[/:id]]',
                    'constant' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\JobHistoryController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'empCurrentPosting' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/empCurrentPosting[/:action[/:id]]',
                    'constant' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EmpCurrentPostingController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'shift' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/shift[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShiftController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'leaveType' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/leaveType[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LeaveTypeController::class,
                        'action' => 'index',
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

            Controller\LeaveTypeController::class => Controller\ControllerFactory::class,
            Controller\ShiftController::class => Controller\ControllerFactory::class,
            Controller\EmpCurrentPostingController::class => Controller\ControllerFactory::class,
            Controller\JobHistoryController::class => Controller\ControllerFactory::class,
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
];