<?php

/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/15/16
 * Time: 1:06 PM
 */

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ArtistController extends AbstractActionController
{

    public function indexAction() {
        return new ViewModel();
    }

}