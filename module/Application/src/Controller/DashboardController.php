<?php

/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/22/16
 * Time: 3:31 PM
 */

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    private $container;
    private $dashboardItems;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->dashboardItems = $container->get("config")['dashboard-items'];
    }

    public function indexAction() {
        $itemDetail = [];

        foreach ($this->dashboardItems as $key => $value) {
            $itemDetail[$key] = [
                "path" => $value,
                "data" => $this->getDashBoardData($key)
            ];
        }
        return new ViewModel([
            'dashboardItems' => [
                $itemDetail
        ]]);
    }

    public function getDashBoardData($item) {
        $data = [];
        switch ($item) {
            case 'holiday-list':
                break;
        }
        return $data;
    }

}
