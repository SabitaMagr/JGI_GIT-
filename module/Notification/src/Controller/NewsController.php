<?php

namespace Notification\Controller;

use Zend\Mvc\Controller\AbstractActionController;


class NewsController extends AbstractActionController {
    
    public function indexAction() {
        echo 'index Action News';
        die();
    }
}