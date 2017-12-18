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
            'email' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/email[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EmailController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'news' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/news[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\NewsController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'news-type' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/news/type[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\NewsTypeController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\NotificationController::class => \Application\Controller\ControllerFactory::class,
            Controller\EmailController::class => \Application\Controller\ControllerFactory::class,
            Controller\NewsController::class => \Application\Controller\ControllerFactory::class,
            Controller\NewsTypeController::class => \Application\Controller\ControllerFactory::class
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
        'news' => [
                [
                'label' => 'news',
                'route' => 'news',
            ],
                [
                'label' => 'news',
                'route' => 'news',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'news',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'news',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'news',
                        'action' => 'edit',
                    ]
                ]
            ]
        ],
        'news-type' => [
                [
                'label' => 'News Type',
                'route' => 'news-type',
            ],
                [
                'label' => 'News Type',
                'route' => 'news-type',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'news-type',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'news-type',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'news-type',
                        'action' => 'edit',
                    ]
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
