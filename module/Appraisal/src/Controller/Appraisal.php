<?php

namespace Appraisal\Controller;

use SelfService\Repository\AdvanceRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Appraisal extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;

//        $repo = new TrainingAssignRepository($adapter);
//        $repo->checkEmployeeTraining(7, Helper::getcurrentExpressionDate());

        $repo = new AdvanceRequestRepository($adapter);
        print gettype($repo->checkAdvance(19, 9)) ;
//                ? "yes" : "no";
        exit;
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
