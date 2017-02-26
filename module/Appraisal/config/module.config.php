<?php

namespace Appraisal;

use Application\Controller\ControllerFactory;
use Application\Factory\DashBoardFactory;
use Appraisal\Controller\AppraisalBackup;
use Appraisal\Controller\Appraisal;
use Appraisal\Controller\EvaluationAndReview;
use Appraisal\Controller\PerformanceAppraisal;
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
                        'controller' => AppraisalBackup::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'appraisal-evaluation-review' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/managerservice/appraisal[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => EvaluationAndReview::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'performance-appraisal' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/selfservice/performanceappraisal[/:action[/:id]]',
                    'constants' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => PerformanceAppraisal::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'navigation' => [
        'appraisal-setup' => [
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
        'appraisal-evaluation-review' => [
                [
                'label' => 'Appraisal',
                'route' => 'appraisal-evaluation-review',
            ],
                [
                'label' => 'Appraisal',
                'route' => 'appraisal-evaluation-review',
                'pages' => [
                        [
                        'label' => 'Evaluation',
                        'route' => 'appraisal-evaluation-review',
                        'action' => 'evaluation',
                    ],
                        [
                        'label' => 'Review',
                        'route' => 'appraisal-evaluation-review',
                        'action' => 'review',
                    ],
                ],
            ],
        ],
        'performance-appraisal' => [
                [
                'label' => 'Performance Appraisal',
                'route' => 'performance-appraisal',
            ],
                [
                'label' => 'Performance Appraisal',
                'route' => 'performance-appraisal',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'performance-appraisal',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AppraisalBackup::class => ControllerFactory::class,
            EvaluationAndReview::class => ControllerFactory::class,
            PerformanceAppraisal::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];

