<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function accessDeniedAction() {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('error/no_access');
        return $viewModel;

//        $view = new ViewModel();
//        $view->setCaptureTo('login');
//        $view->setTerminal(true);
//        $layout = $this->layout();
//        $layout->setTemplate('error/no_access');
//        return $view;
    }

}
