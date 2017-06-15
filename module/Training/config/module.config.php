<?php
namespace Training;

use Application\Controller\ControllerFactory;
use Training\Controller\TrainingAssignController;
use Training\Controller\TrainingStatusController;
use Training\Controller\TrainingApplyController;
use Zend\Router\Http\Segment;
use Training\Controller\TrainingAttendanceController;

return [
    'router' => [
        'routes' => [
            'trainingAssign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/training/trainingAssign[/:action[/:employeeId][/:trainingId]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TrainingAssignController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'trainingStatus' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/training/trainingStatus[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TrainingStatusController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'trainingApply' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/training/trainingApply[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TrainingApplyController::class,
                        'action' => 'add'
                    ],
                ],
            ],
            'trainingAtt' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/training/trainingAtt[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => TrainingAttendanceController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'trainingAssign' => [
                [
                'label' => "Training Assign",
                'route' => "trainingAssign"
            ],
                [
                'label' => "Training Assign",
                'route' => "trainingAssign",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'trainingAssign',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'trainingAssign',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'trainingAssign',
                        'action' => 'view',
                    ],
                    [
                        'label' => 'Add',
                        'route' => 'trainingAssign',
                        'action' => 'assign',
                    ],
                ],
            ],
        ],
        'trainingStatus' => [
                [
                'label' => 'Training Request',
                'route' => 'trainingStatus',
            ],
                [
                'label' => 'Training Request',
                'route' => 'trainingStatus',
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'trainingStatus',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'View',
                        'route' => 'trainingStatus',
                        'action' => 'view',
                    ],
                ]
            ]
        ],
        'trainingApply' => [
                [
                'label' => "Training Request",
                'route' => "trainingApply"
            ],
                [
                'label' => "Training Request",
                'route' => "trainingApply",
                'pages' => [
                        [
                        'label' => 'Add',
                        'route' => 'trainingApply',
                        'action' => 'add',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\TrainingAssignController::class => ControllerFactory::class,
            Controller\TrainingStatusController::class => ControllerFactory::class,
            Controller\TrainingApplyController::class => ControllerFactory::class,
            Controller\TrainingAttendanceController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
