<?php

namespace Appraisal\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;

class Appraisal extends AbstractActionController {

    private $adapter;
    private $logger;

    public function __construct(AdapterInterface $adapter, Logger $logger) {
        $this->adapter = $adapter;
        $this->logger = $logger;
//        $repo = new TrainingAssignRepository($adapter);
//        $repo->checkEmployeeTraining(7, Helper::getcurrentExpressionDate());
//        $repo = new AdvanceRequestRepository($adapter);
//        print $repo->getAdvance(19, 9);
//                ? "yes" : "no";
//        exit;
    }

    public function indexAction() {
        $this->logger->info("test");
        return [];
    }

    public function addAction() {
        
    }

    public function editAction() {
        
    }

    public function reviewAction() {
        
    }

}
