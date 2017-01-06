<?php
namespace Loan;

use Application\Controller\ControllerFactory;
use Loan\Controller\LoanAdvanceStatus;
use Loan\Controller\LoanAdvanceApply;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'loanAdvanceStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/loan/status[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanAdvanceStatus::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'loanAdvanceApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/loan/apply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => LoanAdvanceApply::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'loanAdvanceStatus' => [
                [
                'label' => "Loan/Advance Request",
                'route' => "loanAdvanceStatus"
            ],
                [
                'label' => "Loan/Advance Request",
                'route' => "loanAdvanceStatus",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'loanAdvanceStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'loanAdvanceStatus',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'loanAdvanceStatus',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'loanAdvanceApply' => [
                [
                'label' => "Loan/Advance Apply",
                'route' => "loanAdvanceApply"
            ],
                [
                'label' => "Loan/Advance Apply",
                'route' => "loanAdvanceApply",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'loanAdvanceApply',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'loanAdvanceApply',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'loanAdvanceApply',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\LoanAdvanceStatus::class => ControllerFactory::class,
            Controller\LoanAdvanceApply::class => ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
