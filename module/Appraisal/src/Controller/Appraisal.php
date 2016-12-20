<?php

namespace Appraisal\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Appraisal extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        return [];
    }

    public function addAction() {
        
    }

    public function editAction() {
        
    }

    public function reviewAction() {
        
    }

}
