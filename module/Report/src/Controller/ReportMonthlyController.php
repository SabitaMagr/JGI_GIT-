<?php

namespace Report\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;

class ReportMonthlyController extends AbstractActionController{
    
    
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    
    public function indexAction() {
        
    }
    
}

