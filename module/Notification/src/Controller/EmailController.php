<?php

namespace Notification\Controller;

use Application\Helper\Helper;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EmailController extends AbstractActionController {

    private $employeeId;
    private $adapter;

    const EMAIL_TYPES = [
        1 => "TYPE_ONE",
        2 => "TYPE_TWO",
        3 => "TYPE_THREE"];

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;

        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['emailTypes' => self::EMAIL_TYPES]);
    }

    public function editAction() {
        $request = $this->getRequest();
        print "<pre>";
        print_r($request->getPost());
        exit;
    }

}
