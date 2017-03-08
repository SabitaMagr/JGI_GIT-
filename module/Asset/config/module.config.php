<?php

namespace Asset;

use Application\Controller\ControllerFactory;
use Asset\Controller\GroupController;
use Asset\Controller\SetupController;
use Zend\Router\Http\Segment;

return[
    'router' => [
        'routes' => [
            'assetGroup' => [
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
            
            'assetSetup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/asset/setup[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => SetupController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            
            
        ],
    ],
    
    'navigation' => [
        'assetGroup'=>[
            [
                'label'=>'Asset Type',
                'route'=>'assetGroup',
            ],[
                'label' => 'Asset Type',
                'route' => 'assetGroup',
                'pages' => [
                        [
                        'label' => 'Asset Type List',
                        'route' => 'assetGroup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'assetGroup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'assetGroup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    
    'controllers' => [
        'factories' => [
            GroupController::class => ControllerFactory::class,
            SetupController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

