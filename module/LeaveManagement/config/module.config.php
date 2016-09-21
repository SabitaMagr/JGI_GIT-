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
    'navigation' => [
        'default' => [
            [
                'label' => 'Leave Setup',
                'route' => 'leavesetup',
            ],
            [
                'label' => 'Leave Setup',
                'route' => 'leavesetup',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leavesetup',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leavesetup',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leavesetup',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'leaveassign' => [
            [
                'label' => 'Leave Assign',
                'route' => 'leaveassign',
            ],
            [
                'label' => 'Leave Assign',
                'route' => 'leaveassign',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leaveassign',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leaveassign',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Assign',
                        'route' => 'leaveassign',
                        'action' => 'assign',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaveassign',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
        'leaveapply' => [
            [
                'label' => 'Leave Apply',
                'route' => 'leaveapply',
            ],
            [
                'label' => 'Leave Apply',
                'route' => 'leaveapply',
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'leaveapply',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'leaveapply',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'leaveapply',
                        'action' => 'edit',
                    ],
                ]
            ]
        ],
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


