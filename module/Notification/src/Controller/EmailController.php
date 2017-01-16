<?php

namespace Notification\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EmailController extends AbstractActionController {

    private $employeeId;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;

        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }
    
    
    
}
