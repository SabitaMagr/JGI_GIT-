<?php

namespace Setup;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'employee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/setup/employee[/:action[/:id[/:tab]]]',
                    'defaults' => [
                        'controller' => Controller\EmployeeController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'designation' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/setup/designation[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\DesignationController::class,
                        'action' => 'index'

                    ]
                ]
            ],

            'company' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/setup/company[/:action[/:id]]',
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
                    'route' => '/setup/branch[/:action[/:id]]',
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
                    'route' => '/setup/department[/:action[/:id]]',
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
                    'route' => '/setup/position[/:action[/:id]]',
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
                    'route' => '/setup/serviceType[/:action[/:id]]',
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
                    'route' => '/history/jobHistory[/:action[/:id]]',
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
                    'route' => '/setup/empCurrentPosting[/:action[/:id]]',
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
                    'route' => '/setup/shift[/:action[/:id]]',
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

            'webService' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/setup/webService[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\WebServiceController::class,
                        'action' => 'index',
                    ]
                ],
            ],
//            'leave' => [
//                'type' => segment::class,
//                'options' => [
//                    'route' => '/leave[/:action[/:id]]',
//                    'constants' => [
//                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
//                    ],
//                    'defaults' => [
//                        'controller' => Controller\LeaveMasterController::class,
//                        'action' => 'index',
//                    ]
//                ],
//            ],

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

            Controller\ShiftController::class => Controller\ControllerFactory::class,
            Controller\EmpCurrentPostingController::class => Controller\ControllerFactory::class,
            Controller\JobHistoryController::class => Controller\ControllerFactory::class,
            Controller\WebServiceController::class => Controller\ControllerFactory::class,
//            Controller\LeaveMasterController::class => Controller\ControllerFactory::class,
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];