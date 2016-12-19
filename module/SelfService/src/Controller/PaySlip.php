<?php

namespace SelfService\Controller;

use Application\Helper\Helper;
use Payroll\Repository\RulesRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class PaySlip extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $rulesRepo = new RulesRepository($this->adapter);
        $rulesRaw = $rulesRepo->fetchAll();
        $rules = Helper::extractDbData($rulesRaw);


        $auth = new AuthenticationService();
        $employeeId = $auth->getStorage()->read()['employee_id'];
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
                    'employeeId' => $employeeId
        ]);
    }

}
