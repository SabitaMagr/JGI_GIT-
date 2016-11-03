<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 11:11 AM
 */
namespace SelfService\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;

class Profile extends AbstractActionController {
    public function indexAction()
    {
        $list = "hellwow";
        return Helper::addFlashMessagesToArray($this,['list',$list]);
    }
}