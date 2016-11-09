<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/9/16
 * Time: 5:02 PM
 */
namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;

class ServiceEventTypeController extends AbstractActionController {
    public function indexAction()
    {
        return Helper::addFlashMessagesToArray($this,['list'=>'hellow']);
    }
}