<?php
namespace Travel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class TravelStatus extends AbstractActionController{
    public function __construct() {
    }
    
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
}