<?php
namespace Application\Controller;

use Application\Factory\ConfigInterface;
use Application\Helper\Helper;
use Application\Repository\ForgotPasswordRepository;
use System\Repository\UserSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CheckInController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $appConfig;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    public function setEventManager(EventManagerInterface $events) {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }
    public function indexAction() {
        $userId = $this->params()->fromRoute('userId');
        $userRepository = new UserSetupRepository($this->adapter);
        $userDetail = $userRepository->fetchById($userId)->getArrayCopy();
        return Helper::addFlashMessagesToArray($this, [
                    'username'=> $userDetail['USER_NAME'],
                    'password'=> $userDetail['PASSWORD']
            ]);
    }
}
