<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Training\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Description of TrainingAttendanceController
 *
 * @author root
 */
class TrainingAttendanceController extends AbstractActionController {
    
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function indexAction() {
        
    }
}
