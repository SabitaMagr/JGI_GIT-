<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */
namespace SelfService\Controller;

use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveRequest extends AbstractActionController{
    public function indexAction()
    {
        return new ViewModel();
    }
}