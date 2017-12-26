<?php

namespace MobileApi;

use Application\Controller\ControllerFactory;
use MobileApi\Controller\Authentication;
use MobileApi\Controller\Employee;
use MobileApi\Controller\Leave;
use MobileApi\Controller\Notification;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api-auth' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/auth[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Authentication::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-leave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/leave[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Leave::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-employee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/employee[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Employee::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'api-notification' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/api/notification[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Notification::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Authentication::class => ControllerFactory::class,
            Leave::class => ControllerFactory::class,
            Employee::class => ControllerFactory::class,
            Notification::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];
