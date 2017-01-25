<?php

namespace Appraisal\Controller;

use Application\Helper\Helper;
use SelfService\Repository\TravelRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Appraisal extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;

        $repo = new TravelRequestRepository($adapter);
        $repo->checkEmployeeTravel(7, Helper::getcurrentExpressionDate());
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
