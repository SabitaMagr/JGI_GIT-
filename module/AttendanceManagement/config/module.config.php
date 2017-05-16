<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 11:07 AM
 */
namespace AttendanceManagement;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;


return [
    'router'=>[
        'routes'=>[
            'shiftassign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/shiftassign[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShiftAssign::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'attendancebyhr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/attendancebyhr[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AttendanceByHr::class,
                        'action' => 'index',
                    ]
                ],
            ],

            'shiftsetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/shiftsetup[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShiftSetup::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'attendancestatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/attendancestatus[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AttendanceStatus::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'dailyAttendance'=>[
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/dailyAttendance[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\DailyAttendance::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'calculateOvertime'=>[
                'type' => Segment::class,
                'options' => [
                    'route' => '/attendance/calculateOvertime[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CalculateOvertime::class,
                        'action' => 'index',
                    ]
                ],
            ]
            
        ],
    ],
    'navigation' => [
        'shiftsetup' => [
            [
                'label' => 'Shift',
                'route' => 'shiftsetup',
            ],
            [
                'label' => 'Shift',
                'route' => 'shiftsetup',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'shiftsetup',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'shiftsetup',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'shiftsetup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'attendancebyhr' => [
            [
                'label' => 'Attendance',
                'route' => 'attendancebyhr',
            ],
            [
                'label' => 'Attendance',
                'route' => 'attendancebyhr',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'attendancebyhr',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Entry',
                        'route' => 'attendancebyhr',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'attendancebyhr',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'shiftassign' => [
            [
                'label' => 'Shift Assign',
                'route' => 'shiftassign',
            ],
            [
                'label' => 'Shift Assign',
                'route' => 'shiftassign',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'shiftassign',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'shiftassign',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'shiftassign',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'attendancestatus' => [
            [
                'label' => 'Attendance Request Status',
                'route' => 'attendancestatus',
            ],
            [
                'label' => 'Attendance Request Status',
                'route' => 'attendancestatus',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'attendancestatus',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'attendancestatus',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'attendancestatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'calculateOvertime' => [
            [
                'label' => 'Overtime Report',
                'route' => 'calculateOvertime',
            ],
            [
                'label' => 'Overtime Report',
                'route' => 'calculateOvertime',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'calculateOvertime',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'calculateOvertime',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Detail',
                        'route' => 'calculateOvertime',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\ShiftAssign::class=>ControllerFactory::class,
            Controller\AttendanceByHr::class=>ControllerFactory::class,
            Controller\ShiftSetup::class=>ControllerFactory::class,
            Controller\AttendanceStatus::class=>ControllerFactory::class,
            Controller\DailyAttendance::class=>ControllerFactory::class,
            Controller\CalculateOvertime::class=>ControllerFactory::class,
        ],

    ],

    'view_manager'=>[
        'template_path_stack'=>[
            __DIR__.'/../view',
        ]
    ]
];

