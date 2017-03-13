<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Repository\AppraisalAssignRepository;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class AppraisalAssignController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->repository = new AppraisalAssignRepository($adapter);
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'hellow']);
    }
}