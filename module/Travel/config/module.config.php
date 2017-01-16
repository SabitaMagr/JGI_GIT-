<?php
namespace Travel;

use Application\Controller\ControllerFactory;
use Travel\Controller\TravelStatus;
use Travel\Controller\TravelApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'travelStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/travel/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TravelStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'travelApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/travel/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TravelApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    
    'controllers' => [
        'factories' => [
            Controller\TravelStatus::class => ControllerFactory::class,
            Controller\TravelApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
