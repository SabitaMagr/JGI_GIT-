<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:12 PM
 */

namespace Training;

use Application\Controller\ControllerFactory;
use System\Controller\TrainingAssignController;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'trainingAssign' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/training/trainingAssign[/:action[/:id]]',
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
                        'label' => 'Edit',
                        'route' => 'trainingAssign',
                        'action' => 'edit',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\TrainingAssignController::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
