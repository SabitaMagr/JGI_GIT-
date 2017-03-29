<?php

namespace Report\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class AllReportController extends AbstractActionController{
    
    
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function indexAction(){
        echo 'this is index action controller';
        die();
    }
    
    public function reportOneAction() {
        echo 'this is report one action';
        die();  
    }
    public function reportTwoAction() {
        echo 'this is report Two action';
        die();  
    }
    public function reportThreeAction() {
        echo 'this is repotr three action';
        die();  
    }
    public function reportFourAction() {
        echo 'this is report four action';
        die();  
    }
    
}

