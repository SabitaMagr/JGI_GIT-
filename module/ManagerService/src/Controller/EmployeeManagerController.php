<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Factory\ConfigInterface;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\EmployeeManagerRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EmployeeManagerController extends AbstractActionController {

    private $adapter;
    private $config;
    private $repo;

    public function __construct(AdapterInterface $adapter, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->repo = new EmployeeManagerRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $employeeList = $this->repo->fetchAll();
                return new CustomViewModel(['success' => true, 'data' => $employeeList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, []);
    }

}
