<?php

namespace Notification\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Notification\Model\Notification;
use Notification\Repository\NotificationRepo;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class NotificationController extends AbstractActionController {

    private $notiRepo;
    private $employeeId;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->notiRepo = new NotificationRepo($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $notifications = $this->notiRepo->fetchAllWithEmpDet([Notification::MESSAGE_TO => $this->employeeId]);
        return Helper::addFlashMessagesToArray($this, [
                    "notifications" => Helper::extractDbData($notifications)
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("notification");
        }
        $notification = $this->notiRepo->fetchById($id);
        if ($notification['STATUS'] == 'U') {
            $this->editNotificationStatus($id);
        }

        if ($notification['ROUTE'] != null) {
            $routeJson = (array) json_decode($notification['ROUTE']);
            if (isset($routeJson['route'])) {
                $routeName = $routeJson['route'];
                unset($routeJson['route']);
                return $this->redirect()->toRoute($routeName, $routeJson);
            }
        }
    }

    public function markAsViewedAction() {
        $request = $this->getRequest();
        $response = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->editNotificationStatus($postedData['messageId']);
            $response = ["success" => true];
        } else {
            $response = ["success" => false];
        }
        return new CustomViewModel($response);
    }

    private function editNotificationStatus($id) {
        $notiObj = new Notification();
        $notiObj->status = 'S';
        $this->notiRepo->edit($notiObj, $id);
    }

}
