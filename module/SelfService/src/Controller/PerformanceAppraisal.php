<?php
namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class PerformanceAppraisal extends AbstractActionController{
    private $repository;
    private $adapter;
    private $form;
    private $employeeId;
    
    public function __construct() {
    }
    
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this,['list'=>"hellow"]);
    }
}