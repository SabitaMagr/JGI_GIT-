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
                        'action' => 'index'
                    ]
                ]
            ],
        ]
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Holiday Setup',
                'route' => 'holidaysetup',
            ],
            [
                'label' => 'Holiday Setup',
                'route' => 'holidaysetup',
                'pages' => [
                    [
                        'label' => 'Add',
                        'route' => 'holidaysetup',
                        'action' => 'index',
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


