<?php
/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/22/16
 * Time: 3:31 PM
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

}