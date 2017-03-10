<?php

namespace Asset\Controller;

use Zend\Mvc\Controller\AbstractActionController;


class IssueController extends AbstractActionController
{
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        
    }
    
    public function indexAction() {
        echo 'prabin';
        die();
    }
}

