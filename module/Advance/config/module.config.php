<?php
namespace Advance;

use Advance\Controller\AdvanceApply;
use Advance\Controller\AdvanceSetup;
use Advance\Controller\AdvanceStatus;
use Application\Controller\ControllerFactory;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'advanceStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/advance/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AdvanceStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'advanceApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/advance/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AdvanceApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'advance-setup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/advance/setup[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => AdvanceSetup::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'advanceStatus' => [
                [
                'label' => "Advance Request",
                'route' => "advanceStatus"
            ],
                [
                'label' => "Advance Request",
                'route' => "advanceStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'advanceStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'advanceStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'advanceStatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'advanceApply' => [
                [
                'label' => "Advance Apply",
                'route' => "advanceApply"
            ],
                [
                'label' => "Advance Apply",
                'route' => "advanceApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'advanceApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'advanceApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'advanceApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'advance-setup' => [
                [
                'label' => "Advance Setup",
                'route' => "advance-setup"
            ],
                [
                'label' => "Advance Setup",
                'route' => "advance-setup",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'advance-setup',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'advance-setup',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'advance-setup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AdvanceApply::class => ControllerFactory::class,
            AdvanceStatus::class => ControllerFactory::class,
            AdvanceSetup::class=> ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
