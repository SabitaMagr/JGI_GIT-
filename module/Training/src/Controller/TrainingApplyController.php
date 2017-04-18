<?php
namespace Training\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class TrainingApplyController extends AbstractActionController{
    public function __construct() {
    }
    
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
}