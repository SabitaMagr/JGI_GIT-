<?php

namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use ManagerService\Repository\LoanApproveRepository;
use Zend\Authentication\AuthenticationService;

class LoanApproveController extends AbstractActionController {
    private $loanApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;
        
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->loanApproveRepository = new LoanApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }

    public function indexAction() {
        $result = $this->loanApproveRepository->getAllWidStatus($this->employeeId,'RQ');
        
        return Helper::addFlashMessagesToArray($this, ['list' => 'list']);
    }

}
