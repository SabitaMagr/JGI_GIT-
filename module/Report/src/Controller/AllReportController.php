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
//        echo 'this is index action controller';
//        die();
    }
    
    public function reportOneAction() {

    }
    public function reportTwoAction() {

    }
    public function reportThreeAction() {

    }
    public function reportFourAction() {

    }
    
}

