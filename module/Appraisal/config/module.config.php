<?php

namespace Appraisal;

use Application\Controller\ControllerFactory;
use Appraisal\Controller\Appraisal;
use Zend\Router\Http\Literal;

return [
    'router' => [
        'routes' => [
            'appraisal-setup' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/appraisal[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Appraisal::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Appraisal::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];
