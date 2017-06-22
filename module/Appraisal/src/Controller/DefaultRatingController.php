<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class DefaultRatingController extends AbstractActionController{
    public function __construct() {
    }
    public function indexAction(){
       return Helper::addFlashMessagesToArray($this, ['list'=>"hellow"]);
    }
    public function addAction(){
        
    }
}
