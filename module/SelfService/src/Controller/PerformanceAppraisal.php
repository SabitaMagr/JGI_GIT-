<?php
namespace SelfService\Controller;

use Application\Helper\Helper;
use Appraisal\Repository\AppraisalAssignRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;

class PerformanceAppraisal extends AbstractActionController{
    private $repository;
    private $adapter;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    
    public function indexAction() {
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
//        print_r($this->employeeId); die();
        $result = $appraisalAssignRepo->fetchByEmployeeId($this->employeeId);
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this,['list'=>$list]);
    }
    public function viewAction(){
        return Helper::addFlashMessagesToArray($this,['list'=>'hellow']);
    }
}