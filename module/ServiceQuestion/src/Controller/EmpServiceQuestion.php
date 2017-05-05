<?php

namespace ServiceQuestion\Controller;

use ServiceQuestion\Model\EmpServiceQuestion as EmpServiceQuestionModel;
use ServiceQuestion\Repository\EmpServiceQuestionRepo;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\ServiceEventType;
use Setup\Model\HrEmployees;
use Zend\Authentication\AuthenticationService;

class EmpServiceQuestion extends AbstractActionController {
    private $adapter;
    private $repository;
    private $employeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new EmpServiceQuestionRepo($adapter);
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    
    public function indexAction(){
        return Helper::addFlashMessagesToArray($this, ['list'=>"hellow"]);
    }
    
    public function addAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            
        }
        return Helper::addFlashMessagesToArray($this, [
                'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS=>'E', HrEmployees::RETIRED_FLAG=>'N'], HrEmployees::FIRST_NAME, "ASC", " ", FALSE,TRUE),
                'serviceEventTypes'=> EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS=>'E'], ServiceEventType::SERVICE_EVENT_TYPE_NAME, "ASC", null, FALSE,TRUE)
        ]);
    }
    
    public function editAction(){
        
    }
}
