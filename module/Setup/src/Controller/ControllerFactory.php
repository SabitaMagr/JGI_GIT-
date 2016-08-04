<?php

namespace Setup\Controller;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter=$container->get(AdapterInterface::class);

        $controller = null;
        switch ($requestedName) {
            case BranchController::class:
                $controller = new BranchController($adapter);
                break;
            case CompanyController::class:
                $controller = new CompanyController($adapter);
                break;
            case DepartmentController::class:
                $controller = new DepartmentController($adapter);
                break;
            case PositionController::class:
                $controller = new PositionController($adapter);
                break;
            case ServiceTypeController::class:
                $controller = new ServiceTypeController($adapter);
                break;
            case PositionController::class:
                $controller = new PositionController($adapter);
                break;
        }
        return $controller;
    }
}