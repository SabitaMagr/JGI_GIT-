<?php
namespace Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class LoanAdvanceController extends AbstractActionController{
    public function _construct(){
        
    }
    
    public function indexAction(){
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);       
    } 
    public function addAction(){
        
    }
}