<?php
namespace Setup\Controller;


use Setup\Model\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeControllerFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = new TableGateway('employee', $container->get(AdapterInterface::class));
        $employeeRepository = new EmployeeRepository($table);
        return new EmployeeController($container->get(AdapterInterface::class), $employeeRepository);
    }
}