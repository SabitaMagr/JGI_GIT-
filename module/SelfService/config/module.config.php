<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/15/16
 * Time: 12:55 PM
 */
namespace SelfService;

use Application\Controller\ControllerFactory;
use SelfService\Controller\AdvanceRequest;
use SelfService\Controller\AttendanceRequest;
use SelfService\Controller\Holiday;
use SelfService\Controller\Leave;
use SelfService\Controller\LeaveRequest;
use SelfService\Controller\LoanRequest;
use SelfService\Controller\MyAttendance;
use SelfService\Controller\PaySlip;
use SelfService\Controller\Profile;
use SelfService\Controller\Service;
use SelfService\Controller\TrainingList;
use SelfService\Controller\TravelRequest;
use SelfService\Controller\WorkOnHoliday;
use SelfService\Controller\WorkOnDayoff;
use Zend\Router\Http\Segment;
use SelfService\Controller\PerformanceAppraisal;

return [
    'router' => [
        'routes' => [
            'myattendance' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/myattendance[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MyAttendance::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'holiday' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/holiday[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Holiday::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'leave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/leave[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Leave::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'leaverequest' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/leaverequest[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LeaveRequest::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'attendancerequest' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/attendancerequest[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AttendanceRequest::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'service' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/service[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Service::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'profile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/profile[/:action[/:tab]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Profile::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'payslip' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/payslip[/:action]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => PaySlip::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            'loanRequest' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/loanRequest[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanRequest::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'advanceRequest' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/advanceRequest[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AdvanceRequest::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'trainingList' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/trainingList[/:action[/:employeeId][/:trainingId]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TrainingList::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'travelRequest' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/travelRequest[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TravelRequest::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'workOnHoliday' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/workOnHoliday[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnHoliday::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'workOnDayoff' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/workOnDayoff[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => WorkOnDayoff::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'performanceAppraisal' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/performanceAppraisal[/:action[/:appraisalId]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => PerformanceAppraisal::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'navigation' => [
        'myattendance' => [
            [
                'label' => 'Attendance',
                'route' => 'myattendance',
            ],
            [
                'label' => 'Attendance',
                'route' => 'myattendance',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'myattendance',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Entry',
                        'route' => 'myattendance',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'myattendance',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'holiday' => [
            [
                'label' => 'Holiday',
                'route' => 'holiday',
            ],
            [
                'label' => 'Holiday',
                'route' => 'holiday',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'holiday',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'holiday',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'holiday',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'leave' => [
            [
                'label' => 'Leave',
                'route' => 'leave',
            ],
            [
                'label' => 'Leave',
                'route' => 'leave',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leave',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leave',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leave',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'leaverequest' => [
            [
                'label' => 'Leave Request',
                'route' => 'leaverequest',
            ],
            [
                'label' => 'Leave Request',
                'route' => 'leaverequest',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leaverequest',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leaverequest',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaverequest',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'leaverequest',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'attendancerequest' => [
            [
                'label' => 'Attendance Request',
                'route' => 'attendancerequest',
            ],
            [
                'label' => 'Attendance Request',
                'route' => 'attendancerequest',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'attendancerequest',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'attendancerequest',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'attendancerequest',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'attendancerequest',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'service' => [
            [
                'label' => 'Service',
                'route' => 'service',
            ],
            [
                'label' => 'Service',
                'route' => 'service',
                'pages' => [
                    [
                        'label' => 'History',
                        'route' => 'service',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'service',
                        'action' => 'view',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'service',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'profile' => [
            [
                'label' => 'Profile',
                'route' => 'profile',
            ],
            [
                'label' => 'Profile',
                'route' => 'profile',
                'pages' => [
                    [
                        'label' => 'Detail',
                        'route' => 'profile',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'profile',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'profile',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'payslip' => [
            [
                'label' => 'PaySlip',
                'route' => 'payslip',
            ],
            [
                'label' => 'PaySlip',
                'route' => 'payslip',
                'pages' => [
                    [
                        'label' => 'Detail',
                        'route' => 'payslip',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
        'loanRequest' => [
            [
                'label' => 'Loan Request',
                'route' => 'loanRequest',
            ],
            [
                'label' => 'Loan Request',
                'route' => 'loanRequest',
                'pages' => [
                    [
                        'label' => 'Detail',
                        'route' => 'loanRequest',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'loanRequest',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'loanRequest',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'loanRequest',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'advanceRequest' => [
            [
                'label' => 'Advance Request',
                'route' => 'advanceRequest',
            ],
            [
                'label' => 'Advance Request',
                'route' => 'advanceRequest',
                'pages' => [
                    [
                        'label' => 'Detail',
                        'route' => 'advanceRequest',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'advanceRequest',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'advanceRequest',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'advanceRequest',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'trainingList' => [
            [
                'label' => 'Training List',
                'route' => 'trainingList',
            ],
            [
                'label' => 'Training List',
                'route' => 'trainingList',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'trainingList',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'trainingList',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'trainingList',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'travelRequest' => [
            [
                'label' => 'Travel Request',
                'route' => 'travelRequest',
            ],
            [
                'label' => 'Travel Request',
                'route' => 'travelRequest',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'travelRequest',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'travelRequest',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'travelRequest',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'travelRequest',
                        'action' => 'view',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'travelRequest',
                        'action' => 'viewExpense',
                    ],
                    [
                        'label' => 'For Expense',
                        'route' => 'travelRequest',
                        'action' => 'expenseRequest',
                    ],
                ],
            ],
        ],
        'workOnHoliday' => [
            [
                'label' => 'Work on Holiday Request',
                'route' => 'workOnHoliday',
            ],
            [
                'label' => 'Work on Holiday Request',
                'route' => 'workOnHoliday',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'workOnHoliday',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'workOnHoliday',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'workOnHoliday',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'workOnDayoff' => [
            [
                'label' => 'Work on Day-off Request',
                'route' => 'workOnDayoff',
            ],
            [
                'label' => 'Work on Day-off Request',
                'route' => 'workOnDayoff',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'workOnDayoff',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'workOnDayoff',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'workOnDayoff',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'performanceAppraisal' => [
            [
                'label' => 'Performance Appraisal',
                'route' => 'performanceAppraisal',
            ],
            [
                'label' => 'Performance Appraisal',
                'route' => 'performanceAppraisal',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'performanceAppraisal',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'performanceAppraisal',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'performanceAppraisal',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            MyAttendance::class => ControllerFactory::class,
            Holiday::class => ControllerFactory::class,
            Leave::class => ControllerFactory::class,
            LeaveRequest::class => ControllerFactory::class,
            AttendanceRequest::class => ControllerFactory::class,
            Profile::class => ControllerFactory::class,
            Service::class => ControllerFactory::class,
            PaySlip::class => ControllerFactory::class,
            LoanRequest::class => ControllerFactory::class,
            TrainingList::class => ControllerFactory::class,
            AdvanceRequest::class => ControllerFactory::class,
            TravelRequest::class => ControllerFactory::class,
            WorkOnHoliday::class => ControllerFactory::class,
            WorkOnDayoff::class => ControllerFactory::class,
            PerformanceAppraisal::class=> ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];