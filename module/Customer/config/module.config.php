<?php

namespace Customer;

use Application\Controller\ControllerFactory;
use Customer\Controller\CustomerContract;
use Customer\Controller\CustomerSetup;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'customer-setup' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/customer/setup[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => CustomerSetup::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'customer-contract' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/customer/contract[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => CustomerContract::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'customer-setup' => [
            [
                'label' => "Customer",
                'route' => "customer-setup"
            ],
            [
                'label' => "Customer",
                'route' => "customer-setup",
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'customer-setup',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'customer-setup',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'customer-setup',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'customer-contract' => [
            [
                'label' => "Customer Contract",
                'route' => "customer-contract"
            ],
            [
                'label' => "Customer Contract",
                'route' => "customer-contract",
                'pages' => [
                    [
                        'label' => 'List',
                        'route' => 'customer-contract',
                        'action' => 'index',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'customer-contract',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'customer-contract',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            CustomerSetup::class => ControllerFactory::class,
            CustomerContract::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
