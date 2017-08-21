<?php

namespace Appraisal\Controller;

use Application\Factory\ConfigInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;

class AppraisalBackup extends AbstractActionController {

    private $adapter;
    private $logger;

    /**
     * InitCap
     */
    public function __construct(AdapterInterface $adapter, Logger $logger, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->logger = $logger;
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
