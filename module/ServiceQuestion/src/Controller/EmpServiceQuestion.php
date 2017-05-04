<?php

namespace ServiceQuestion\Controller;

use ServiceQuestion\Model\EmpServiceQuestion as EmpServiceQuestionModel;
use ServiceQuestion\Repository\EmpServiceQuestionRepo;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;

class EmpServiceQuestion extends AbstractActionController {

    public function __construct(AdapterInterface $adapter) {
        
    }
    
    public function indexAction(){
        return Helper::addFlashMessagesToArray($this, ['list'=>"hellow"]);
    }
    
    public function addAction(){
        
    }
    
    public function editAction(){
        
    }
}
