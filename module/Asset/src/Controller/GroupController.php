<?php

namespace Asset\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class GroupController extends AbstractActionController{
    
    public function __construct(AdapterInterface $adapter) {
        
    }

        public function indexAction(){
        echo 'prabin';
        die();
    }
    
    
    
}
