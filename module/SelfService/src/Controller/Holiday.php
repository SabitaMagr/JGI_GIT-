<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:36 PM
 */
namespace SelfService\Controller;

use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;


class Holiday extends AbstractActionController
{
    private $authService;
    private $user_id;
    private $employee_id;

    public function __construct()
    {
        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employee_id = $recordDetail['employee_id'];
    }

    public function indexAction()
    {

        return new ViewModel(["username" => $this->employee_id]);
    }
}