<?php
namespace WorkOnDayoff\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class Apply extends AbstractActionController{
    public function __construct(){
        
    }
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'hellow day off apply']);
    }
}