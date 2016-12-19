<?php

namespace SelfService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Payroll\Model\Rules;
use Payroll\Repository\RulesRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
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
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
        ]);
    }

}
