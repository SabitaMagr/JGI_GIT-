<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 3:26 PM
 */
namespace LeaveManagement\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveBalance extends AbstractActionController {
    public function indexAction()
    {
        $list = "hellow";
        return Helper::addFlashMessagesToArray($this,['list'=>$list]);
    }
}