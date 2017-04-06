<?php
namespace Test;

use Application\Controller\ControllerFactory;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            Controller\TestController::class => ControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'test' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/test',
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                        'action'=>'index'
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
             __DIR__ . '/../view',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ],
];



