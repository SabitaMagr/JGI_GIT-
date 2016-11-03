<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 11:12 AM
 */
namespace SelfService\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;

class Service extends AbstractActionController {
    public function indexAction()
    {
        $list = "hellow";
        return Helper::addFlashMessagesToArray($this,['list',$list]);
    }
}