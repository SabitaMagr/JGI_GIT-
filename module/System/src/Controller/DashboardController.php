<?php

namespace System\Controller;

use Interop\Container\ContainerInterface;
use System\Repository\RoleSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class DashboardController extends AbstractActionController {

    private $container;
    private $adapter;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->adapter = $this->container->get(AdapterInterface::class);
    }

    public function indexAction() {
        $roleRepo = new RoleSetupRepository($this->adapter);
        print "<pre>";
        print_r($roleRepo->fetchAll());
        exit;
        $dashboardItems = $this->container->get("config")['dashboard-items'];
        return ['dashboardItems' => $dashboardItems];
    }

}
