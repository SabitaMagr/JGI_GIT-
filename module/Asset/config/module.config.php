<?php

namespace Asset;

use Application\Controller\ControllerFactory;
use Asset\Controller\GroupController;
use Zend\Router\Http\Segment;

return[
    'router' => [
        'routes' => [
            'group' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/asset/group[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => GroupController::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            GroupController::class => ControllerFactory::class,
//            Controller\LoanApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

