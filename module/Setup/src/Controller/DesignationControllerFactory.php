<?php

namespace Setup\Controller;


use Setup\Model\DesignationRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

class DesignationControllerFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = new TableGateway('designation', $container->get(AdapterInterface::class));
        $designationRepository = new DesignationRepository($table);
        return new DesignationController( $designationRepository);
    }
}