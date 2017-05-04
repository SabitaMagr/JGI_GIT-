<?php
namespace ServiceQuestion;

use Application\Controller\ControllerFactory;
use ServiceQuestion\Controller\ResignationQuestion;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'resignationQuestion' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/serviceQuestion/resignation[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => ResignationQuestion::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'resignationQuestion' => [
                [
                'label' => "Employee Service Question for Resignation",
                'route' => "resignationQuestion"
            ],
                [
                'label' => "Employee Service Question for Resignation",
                'route' => "resignationQuestion",
                'pages' => [
                        [
                        'label' => 'List',
                        'route' => 'resignationQuestion',
                        'action' => 'index',
                    ],
                        [
                        'label' => 'Add',
                        'route' => 'resignationQuestion',
                        'action' => 'add',
                    ],
                        [
                        'label' => 'Detail',
                        'route' => 'resignationQuestion',
                        'action' => 'view',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ResignationQuestion::class => ControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
