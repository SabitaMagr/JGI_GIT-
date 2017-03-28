<?php

namespace Notification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;


class NewsController extends AbstractActionController {
    
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function indexAction() {
//        echo 'index Action News';
//        die();
    }
}