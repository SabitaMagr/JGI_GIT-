<?php

namespace Asset\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;


class SetupController extends AbstractActionController {

    private $adapter;
//    private $repository;
//    private $form;
//    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
//        $this->repository = new GroupRepository($adapter);
//        $auth = new AuthenticationService();
//        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        echo 'setup prabin';
        die();
    }

}
