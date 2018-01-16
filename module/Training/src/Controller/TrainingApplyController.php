<?php

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;

namespace Training\Controller;

class TrainingApplyController extends AbstractActionController {

    public function __construct() {
        
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list' => 'list']);
    }

}
