<?php

namespace LeaveManagement;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'leavesetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave/leavesetup[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\LeaveSetup::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leaveassign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave/leaveassign[/:action[/:eid[/:id]]]',
                    'defaults' => [
                        'controller' => Controller\leaveAssign::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'leaveapply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave/leaveapply[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\LeaveApply::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\LeaveSetup::class => ControllerFactory::class,
            Controller\leaveAssign::class => ControllerFactory::class,
            Controller\LeaveApply::class => ControllerFactory::class,
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


