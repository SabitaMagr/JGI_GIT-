<?php
namespace Setup\Factory;

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 5:13 PM
 */
use Zend\ServiceManager\Factory\FactoryInterface;
use Setup\Controller\EmployeeController;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\EmployeeRepository;
use Zend\Db\TableGateway\TableGateway;

class EmployeeControllerFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = new TableGateway('employee', $container->get(AdapterInterface::class));
        $employeeRepository = new EmployeeRepository($table);
        return new EmployeeController($container->get(AdapterInterface::class), $employeeRepository);
    }
}