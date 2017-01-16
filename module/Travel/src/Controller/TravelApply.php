<?php
namespace Travel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class TravelApply extends AbstractActionController{
    public function __construct(){
        
    }
    public function addAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
}