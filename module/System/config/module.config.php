<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:12 PM
 */

namespace System;

use Application\Controller\ControllerFactory;
use Application\Factory\DashBoardFactory;
use System\Controller\DashboardController;
use System\Controller\MenuSetupController;
use System\Controller\RoleSetupController;
use System\Controller\UserSetupController;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'rolesetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/system/rolesetup[/:action[/:id][/:role]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => RoleSetupController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'usersetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/system/usersetup[/:action[/:id][/:role]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => UserSetupController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'menusetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/system/menusetup[/:action[/:id][/:role]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => MenuSetupController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'dashboardsetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/system/dashboard[/:action[/:id]]',
                    'constraint' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => DashboardController::class,
                        'action' => 'index'
                    ]
                ]
            ]
        ],
    ],
    'navigation' => [
        'rolesetup' => [
                [
                'label' => "Role Setup",
                'route' => "rolesetup"
            ],
                [
                'label' => "Role Setup",
                'route' => "rolesetup",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'rolesetup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'rolesetup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'rolesetup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'usersetup' => [
                [
                'label' => "User Setup",
                'route' => "usersetup"
            ],
                [
                'label' => "User Setup",
                'route' => "usersetup",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'usersetup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'usersetup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'usersetup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'menusetup' => [
                [
                'label' => "Menu Setup",
                'route' => "menusetup"
            ],
                [
                'label' => "Menu Setup",
                'route' => "menusetup",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'menusetup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'menusetup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'menusetup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'dashboardsetup' => [
                [
                'label' => "Dashboard Setup",
                'route' => "dashboardsetup"
            ],
                [
                'label' => "Dashboard Setup",
                'route' => "dashboardsetup",
                'pages' => [
                        [
                        'label' => 'Assign Dashboard',
                        'route' => 'dashboardsetup',
                        'action' => 'index',
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            RoleSetupController::class => ControllerFactory::class,
            UserSetupController::class => ControllerFactory::class,
            MenuSetupController::class => ControllerFactory::class,
            DashboardController::class => DashBoardFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
