<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 4:59 PM
 */

namespace ManagerService;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'leaveapprove' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/managerservice/leaveapprove[/:action[/:id][/:role]]',
                    'defaults' => [
                        'controller' => Controller\LeaveApproveController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'attedanceapprove'=>[
                'type' => Segment::class,
                'options' => [
                    'route' => '/managerservice/attendanceapprove[/:action[/:id][/:role]]',
                    'defaults' => [
                        'controller' => Controller\AttendanceApproveController::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'navigation' => [
        'leaveapprove' => [
            [
                'label' => 'Leave Request',
                'route' => 'leaveapprove',
            ],
            [
                'label' => 'Leave Request',
                'route' => 'leaveapprove',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leaveapprove',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'List',
                        'route' => 'leaveapprove',
                        'action' => 'status',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaveapprove',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'View',
                        'route' => 'leaveapprove',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'attedanceapprove' => [
            [
                'label' => 'Attendance Request',
                'route' => 'attedanceapprove',
            ],
            [
                'label' => 'Attendance Request',
                'route' => 'attedanceapprove',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'attedanceapprove',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'List',
                        'route' => 'attedanceapprove',
                        'action' => 'status',
                    ],
                    [
                        'label' => 'View',
                        'route' => 'attedanceapprove',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\LeaveApproveController::class => ControllerFactory::class,
            Controller\AttendanceApproveController::class=>ControllerFactory::class
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


