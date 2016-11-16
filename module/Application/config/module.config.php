<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'console' => [
        'router' => [
            'routes' => [
                'user-reset-password' => [
                    'options' => [
                        'route' => 'attendance daily-attendance',
                        'defaults' => [
                            'controller' => Controller\CronController::class,
                            'action' => 'index',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'auth' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/auth',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'process' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'logout',
                    ],
                ],
            ],
            'dashboard' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/dashboard',
                    'defaults' => [
                        'controller' => Controller\DashboardController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'process' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [],
                        ],
                    ],
                ],
            ]
        ],
    ],
    'navigation' => [
        'navigation-example' => [
                [
                'label' => 'Google',
                'uri' => 'https://www.google.com',
                'target' => '_blank'
            ],
                [
                'label' => 'Home',
                'route' => 'leavesetup'
            ],
                [
                'label' => 'Modules',
                'uri' => '#',
                'pages' => [
                        [
                        'label' => 'LearnZF2Ajax',
                        'route' => 'leavesetup'
                    ],
                        [
                        'label' => 'LearnZF2FormUsage',
                        'route' => 'leavesetup'
                    ],
                        [
                        'label' => 'LearnZF2Barcode',
                        'route' => 'leavesetup'
                    ],
                        [
                        'label' => 'LearnZF2Pagination',
                        'route' => 'leavesetup'
                    ],
                        [
                        'label' => 'LearnZF2Log',
                        'route' => 'leavesetup'
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation-menu' => 'Application\Navigation\NavigationFactory',
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\DashboardController::class => Factory\DashBoardFactory::class,
            Controller\CronController::class => Controller\ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login' => __DIR__ . '/../view/layout/login.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/no_access' => __DIR__ . '/../view/error/no_access.phtml',
            'partial/header' => __DIR__ . '/../view/layout/partials/header.phtml',
            'partial/footer' => __DIR__ . '/../view/layout/partials/footer.phtml',
            'partial/sidebar' => __DIR__ . '/../view/layout/partials/sidebar.phtml',
            'partial/breadcrumb' => __DIR__ . '/../view/layout/partials/breadcrumb.phtml',
            'partial/profile' => __DIR__ . '/../view/layout/partials/profile.phtml',
            'dashboard-item/holiday-list' => __DIR__ . '/../view/layout/dashboard-items/holiday-list.phtml',
            'dashboard-item/attendance-request' => __DIR__ . '/../view/layout/dashboard-items/attendance-request.phtml',
            'dashboard-item/leave-apply' => __DIR__ . '/../view/layout/dashboard-items/leave-apply.phtml',
            'dashboard-item/present-absent' => __DIR__ . '/../view/layout/dashboard-items/present-absent.phtml',
            'dashboard-item/employee-count-by-branch' => __DIR__ . '/../view/layout/dashboard-items/employee-count-by-branch.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'dashboard-items' => [
        'holiday-list' => 'dashboard-item/holiday-list',
        'attendance-request' => 'dashboard-item/attendance-request',
        'leave-apply' => 'dashboard-item/leave-apply',
        'present-absent' => 'dashboard-item/present-absent',
        'emp-cnt-by-branch' => 'dashboard-item/employee-count-by-branch'
    ],
];
