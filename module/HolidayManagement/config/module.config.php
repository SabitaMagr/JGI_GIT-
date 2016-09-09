<?php

namespace HolidayManagement;

use Zend\Router\Http\Segment;
use Setup\Controller\ControllerFactory;

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
    'controllers' => [
        'factories' => [
            Controller\HolidaySetup::class=>ControllerFactory::class,
        ],

    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];


