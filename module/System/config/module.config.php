<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:12 PM
 */
namespace System;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

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
                        'controller' => Controller\RoleSetupController::class,
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
                        'controller' => Controller\UserSetupController::class,
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
                        'controller' => Controller\MenuSetupController::class,
                        'action' => 'index'
                    ],
                ],
            ],
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
    ],

    'controllers' => [
        'factories' => [
            Controller\RoleSetupController::class => ControllerFactory::class,
            Controller\UserSetupController::class => ControllerFactory::class,
            Controller\MenuSetupController::class => ControllerFactory::class,
        ],

    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];