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
            'serviceEventType'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/serviceEventType[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ServiceEventTypeController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'academicDegree'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/academicDegree[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AcademicDegreeController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'academicUniversity'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/academicUniversity[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AcademicUniversityController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'academicProgram'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/academicProgram[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AcademicProgramController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'academicCourse'=>[
                'type'=>segment::class,
                'options'=>[
                    'route'=>'/setup/academicCourse[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AcademicCourseController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'training' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/setup/training[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TrainingController::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'loan' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/setup/loan[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoanController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'advance' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/setup/advance[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdvanceController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            'institute' => [
                'type' => segment::class,
                'options' => [
                    'route' => '/setup/institute[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\InstituteController::class,
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
        'employee' => [
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
                    [
                        'label' => 'Detail',
                        'route' => 'employee',
                        'action' => 'view',
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
                'label' => 'Service Status Update',
                'route' => 'jobHistory',
            ],
            [
                'label' => 'Service Status Update',
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
                'label' => 'Reporting Hierarchy',
                'route' => 'recommendapprove',
            ],
            [
                'label' => 'Reporting Hierarchy',
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
                    [
                        'label' => 'Group Assign',
                        'route' => 'recommendapprove',
                        'action' => 'groupAssign',
                    ],
                ]
            ]
        ],
        'serviceEventType' => [

            [
                'label' => 'Service Event Type',
                'route' => 'serviceEventType',
            ],
            [
                'label' => 'Service Event Type',
                'route' => 'serviceEventType',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'serviceEventType',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'serviceEventType',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'serviceEventType',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'academicDegree' => [

            [
                'label' => 'Academic Degree',
                'route' => 'academicDegree',
            ],
            [
                'label' => 'Academic Degree',
                'route' => 'academicDegree',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'academicDegree',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'academicDegree',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'academicDegree',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'academicUniversity' => [

            [
                'label' => 'Academic University',
                'route' => 'academicUniversity',
            ],
            [
                'label' => 'Academic University',
                'route' => 'academicUniversity',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'academicUniversity',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'academicUniversity',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'academicUniversity',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'academicProgram' => [

            [
                'label' => 'Academic Program',
                'route' => 'academicProgram',
            ],
            [
                'label' => 'Academic Program',
                'route' => 'academicProgram',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'academicProgram',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'academicProgram',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'academicProgram',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'academicCourse' => [

            [
                'label' => 'Academic Course',
                'route' => 'academicCourse',
            ],
            [
                'label' => 'Academic Course',
                'route' => 'academicCourse',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'academicCourse',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'academicCourse',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'academicCourse',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'training' => [

            [
                'label' => 'Training',
                'route' => 'training',
            ],
            [
                'label' => 'Training',
                'route' => 'training',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'training',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'training',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'training',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'loan' => [

            [
                'label' => 'Loan',
                'route' => 'loan',
            ],
            [
                'label' => 'Loan',
                'route' => 'loan',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'loan',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'loan',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'loan',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'advance' => [
            [
                'label' => 'Advance',
                'route' => 'advance',
            ],
            [
                'label' => 'Advance',
                'route' => 'advance',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'advance',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'advance',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'advance',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'institute' => [
            [
                'label' => 'Institute',
                'route' => 'institute',
            ],
            [
                'label' => 'Institute',
                'route' => 'institute',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'institute',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'institute',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'institute',
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
            Controller\RecommendApproveController::class=>ControllerFactory::class,
            Controller\ServiceEventTypeController::class=>ControllerFactory::class,
            Controller\AcademicDegreeController::class=>ControllerFactory::class,
            Controller\AcademicUniversityController::class=>ControllerFactory::class,
            Controller\AcademicProgramController::class=>ControllerFactory::class,
            Controller\AcademicCourseController::class=>ControllerFactory::class,
            Controller\TrainingController::class=>ControllerFactory::class,
            Controller\LoanController::class=>ControllerFactory::class,
            Controller\AdvanceController::class=> ControllerFactory::class,
            Controller\InstituteController::class=> ControllerFactory::class
//            Controller\LeaveMasterController::class => Controller\ControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];