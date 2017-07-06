<?php
namespace Notification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

class BasePathDetail extends AbstractActionController {
    public function __construct(){
        
    }
    public static function getBasePath(){
        $self = new static;
        $event = $self->getEvent();
        print_r($event); die();
        $cur = $event->getRequest()->getRequestUri();
        return $cur;
        
    }
}

