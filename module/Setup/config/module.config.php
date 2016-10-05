<?php

namespace Setup;

use Application\Controller\ControllerFactory;
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
            'recommendapprove'=>[
              'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/recommendapprove[/:action[/:id]]',
                    'constants'=>[
                      'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\RecommendApproveController::class,
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
    'navigation' => [
        'default' => [
            [
                'label' => 'Employee',
                'route' => 'employee',
            ],
            [
                'label' => 'Employee',
                'route' => 'employee',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'employee',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'employee',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'employee',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'designation' => [

            [
                'label' => 'Designation',
                'route' => 'designation',
            ],
            [
                'label' => 'Designation',
                'route' => 'designation',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'designation',
                        'action' => 'index',
                    ],

                    [
                        'label' => 'Add',
                        'route' => 'designation',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'designation',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'company' => [

            [
                'label' => 'Company',
                'route' => 'company',
            ],
            [
                'label' => 'Company',
                'route' => 'company',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'company',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'company',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'company',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'branch' => [

            [
                'label' => 'Branch',
                'route' => 'branch',
            ],
            [
                'label' => 'Branch',
                'route' => 'branch',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'branch',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'branch',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'branch',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'department' => [

            [
                'label' => 'Department',
                'route' => 'department',
            ],
            [
                'label' => 'Department',
                'route' => 'department',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'department',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'department',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'department',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'position' => [

            [
                'label' => 'Position',
                'route' => 'position',
            ],
            [
                'label' => 'Position',
                'route' => 'position',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'position',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'position',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'position',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'serviceType' => [

            [
                'label' => 'Service Type',
                'route' => 'serviceType',
            ],
            [
                'label' => 'Service Type',
                'route' => 'serviceType',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'serviceType',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'serviceType',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'serviceType',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'empCurrentPosting' => [

            [
                'label' => 'Employee Current Posting',
                'route' => 'empCurrentPosting',
            ],
            [
                'label' => 'Employee Current Posting',
                'route' => 'empCurrentPosting',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'empCurrentPosting',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'empCurrentPosting',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'empCurrentPosting',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'jobHistory' => [

            [
                'label' => 'Job History',
                'route' => 'jobHistory',
            ],
            [
                'label' => 'Job History',
                'route' => 'jobHistory',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'jobHistory',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'jobHistory',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'jobHistory',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'recommendapprove' => [

            [
                'label' => 'Recommender And Approver',
                'route' => 'recommendapprove',
            ],
            [
                'label' => 'Recommender And Approver',
                'route' => 'recommendapprove',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'recommendapprove',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'recommendapprove',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'recommendapprove',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],

    ],
    'controllers' => [
        'factories' => [
            Controller\EmployeeController::class => ControllerFactory::class,
            Controller\DesignationController::class => ControllerFactory::class,
            Controller\CompanyController::class => ControllerFactory::class,
            Controller\BranchController::class => ControllerFactory::class,
            Controller\DepartmentController::class => ControllerFactory::class,
            Controller\PositionController::class => ControllerFactory::class,
            Controller\ServiceTypeController::class => ControllerFactory::class,
            Controller\EmpCurrentPostingController::class => ControllerFactory::class,
            Controller\JobHistoryController::class => ControllerFactory::class,
            Controller\WebServiceController::class => ControllerFactory::class,
            Controller\RecommendApproveController::class=>ControllerFactory::class
//            Controller\LeaveMasterController::class => Controller\ControllerFactory::class,
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];