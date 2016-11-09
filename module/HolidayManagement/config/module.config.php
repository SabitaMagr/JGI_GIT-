<?php

namespace HolidayManagement;

use Zend\Router\Http\Segment;
use Application\Controller\ControllerFactory;

return [
    'router' => [
        'routes' => [
            'holidaysetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/holiday/holidaysetup[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\HolidaySetup::class,
                        'action' => 'list'
                    ]
                ]
            ],
        ]
    ],
    'navigation' => [
        'holidaysetup' => [
            [
                'label' => 'Holiday',
                'route' => 'holidaysetup',
            ],
            [
                'label' => 'Holiday',
                'route' => 'holidaysetup',
                'pages' => [
                    [
                        'label' => 'Detail',
                        'route' => 'holidaysetup',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'holidaysetup',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'List',
                        'route' => 'holidaysetup',
                        'action' => 'list',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'holidaysetup',
                        'action' => 'edit',
                    ],
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\HolidaySetup::class => ControllerFactory::class,
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


