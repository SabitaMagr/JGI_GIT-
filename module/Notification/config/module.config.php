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
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
