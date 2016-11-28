<?php

namespace System\Controller;

use Interop\Container\ContainerInterface;
use PHPixie\Image;
use System\Repository\RoleSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class DashboardController extends AbstractActionController {

    private $container;
    private $adapter;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->adapter = $this->container->get(AdapterInterface::class);
        $image = new Image();
        $data = file_get_contents('/var/www/html/neo/neo-hris-metronic/public/uploads/1479968105.jpg');
        $img = $image->load($data);
        $img->resize(29, 29,true);
        $img->save('/var/www/html/neo/neo-hris-metronic/public/uploads/1479968105_q.jpg');
    }

    public function indexAction() {
        $roleRepo = new RoleSetupRepository($this->adapter);
        $rolesRaw = $roleRepo->fetchAll();
        $roles = [];
        foreach ($rolesRaw as $value) {
            array_push($roles, $value);
        }
        $dashboardItems = $this->container->get("config")['dashboard-items'];
        $roleTypes = $this->container->get("config")['role-types'];
        return ['dashboardItems' => $dashboardItems, 'roles' => $roles, 'roleTypes' => $roleTypes];
    }

}
