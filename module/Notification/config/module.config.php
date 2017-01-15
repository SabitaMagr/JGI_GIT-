<?php

namespace Notification;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'notification' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/notification[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\NotificationController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\NotificationController::class => \Application\Controller\ControllerFactory::class
        ],
    ],
    'navigation' => [
        'notification' => [
                [
                'label' => 'Notification',
                'route' => 'notification',
            ],
                [
                'label' => 'Notification',
                'route' => 'notification',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'notification',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'View',
                        'route' => 'notification',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
