<?php
namespace Loan;

use Application\Controller\ControllerFactory;
use Loan\Controller\LoanStatus;
use Loan\Controller\LoanApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'loanStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/loan/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'loanApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/loan/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'loanStatus' => [
                [
                'label' => "Loan Request",
                'route' => "loanStatus"
            ],
                [
                'label' => "Loan Request",
                'route' => "loanStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'loanStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'loanStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'loanStatus',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
        'loanApply' => [
                [
                'label' => "Loan Apply",
                'route' => "loanApply"
            ],
                [
                'label' => "Loan/Advance Apply",
                'route' => "loanApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'loanApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'loanApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'loanApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\LoanStatus::class => ControllerFactory::class,
            Controller\LoanApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
