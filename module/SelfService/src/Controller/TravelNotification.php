<?php
namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;

class TravelNotification extends AbstractActionController{
    public function __construct(AdapterInterface $adapter) {
    }
}