<?php
namespace Setup\Controller;

use Setup\Model\PositionRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;


class PositionControllerFactory implements FactoryInterface
{

	public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options=null){
		$table = new TableGateway('position',$container->get(AdapterInterface::class));
		$positionRepository = new PositionRepository($table);
		return new PositionController($positionRepository);
	}

	
}
