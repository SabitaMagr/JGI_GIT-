<?php

namespace Appraisal;

use Application\Controller\ControllerFactory;
use Application\Factory\DashBoardFactory;
use Appraisal\Controller\AppraisalBackup;
use Appraisal\Controller\Appraisal;
use Appraisal\Controller\EvaluationAndReview;
use Appraisal\Controller\PerformanceAppraisal;
use Appraisal\Controller\TypeController;
use Zend\Router\Http\Segment;
use Appraisal\Controller\StageController;
use Appraisal\Controller\HeadingController;
use Appraisal\Controller\QuestionController;

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
            'type'=>[
                'type'=>Segment::class,
                'options'=>[
                    'route'=> '/appraisal/type[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=>TypeController::class,
                        'action'=>'index'
                    ]
                ],
            ],
            'stage'=>[
                'type'=>Segment::class,
                'options'=>[
                    'route'=> '/appraisal/stage[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=> StageController::class,
                        'action'=>'index'
                    ]
                ],
            ],
            'heading'=>[
                'type'=>Segment::class,
                'options'=>[
                    'route'=> '/appraisal/heading[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=> HeadingController::class,
                        'action'=>'index'
                    ]
                ],
            ],
            'question'=>[
                'type'=>Segment::class,
                'options'=>[
                    'route'=> '/appraisal/question[/:action[/:id]]',
                    'constants'=>[
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]+',
                    ],
                    'defaults'=>[
                        'controller'=> QuestionController::class,
                        'action'=>'index'
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
        'Type'=>[
            [
                'label'=>'Appraisal Type',
                'route'=>'type',
            ],[
                'label' => 'Appraisal Type',
                'route' => 'type',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'type',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'type',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'type',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'Stage'=>[
            [
                'label'=>'Appraisal Stage',
                'route'=>'stage',
            ],[
                'label' => 'Appraisal Stage',
                'route' => 'stage',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'stage',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'stage',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'stage',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'Heading'=>[
            [
                'label'=>'Appraisal Heading',
                'route'=>'heading',
            ],[
                'label' => 'Appraisal Heading',
                'route' => 'heading',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'heading',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'heading',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'heading',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
        'Question'=>[
            [
                'label'=>'Appraisal Question',
                'route'=>'question',
            ],[
                'label' => 'Appraisal Question',
                'route' => 'question',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'question',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'question',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Edit',
                        'route' => 'question',
                        'action' => 'edit',
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
            TypeController::class => ControllerFactory::class,
            StageController::class => ControllerFactory::class,
            HeadingController::class=> ControllerFactory::class,
            QuestionController::class=> ControllerFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ]
];

