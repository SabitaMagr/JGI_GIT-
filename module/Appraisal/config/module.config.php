<?php

namespace Appraisal;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'appraisal-setup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/appraisal[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\Appraisal::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'navigation' => [
        'shiftsetup' => [
                [
                'label' => 'Appraisal',
                'route' => 'appraisal-setup',
            ],
                [
                'label' => 'Appraisal',
                'route' => 'appraisal-setup',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'appraisal-setup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'appraisal-setup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'appraisal-setup',
                        'action' => 'edit',
                    ],
                        [
                        'label' => 'Review',
                        'route' => 'appraisal-setup',
                        'action' => 'review',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\Appraisal::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];

