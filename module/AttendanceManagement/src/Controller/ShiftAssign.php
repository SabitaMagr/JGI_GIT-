<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 11:09 AM
 */

namespace AttendanceManagement\Controller;


use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Mvc\Console\View\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class ShiftAssign extends AbstractActionController {

    public function indexAction()
    {
      return new ViewModel();
    }
}