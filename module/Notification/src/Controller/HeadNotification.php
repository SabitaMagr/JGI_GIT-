<?php

namespace Notification\Controller;

use Application\Helper\Helper;
use Application\Model\Model;
use LeaveManagement\Model\LeaveApply;
use Notification\Model\Notification;
use Notification\Model\NotificationEvents;
use Notification\Repository\NotificationRepo;
use Setup\Repository\RecommendApproveRepository;
use Zend\Db\Adapter\AdapterInterface;

class HeadNotification {

    const EXPIRE_IN = 14;

    private $adapter;

    public static function getNotifications(AdapterInterface $adapter, int $empId) {
        $notiRepo = new NotificationRepo($adapter);
        $notifications = $notiRepo->fetchAllBy([Notification::MESSAGE_TO => $empId, Notification::STATUS => 'U']);
        return Helper::extractDbData($notifications);
    }

    private static function addNotifications(string $title, string $desc, int $from, int $to, string $route, AdapterInterface $adapter) {
        $notificationRepo = new NotificationRepo($adapter);
        $notification = new Notification();
        $notification->messageTitle = $title;
        $notification->messageDesc = $desc;
        $notification->messageFrom = $from;
        $notification->messageTo = $to;
        $notification->route = $route;
        $notification->messageId = ((int) Helper::getMaxId($adapter, Notification::TABLE_NAME, Notification::MESSAGE_ID)) + 1;
        $notification->messageDateTime = Helper::getcurrentExpressionDate();
        $notification->expiryTime = Helper::getExpressionDate(date(Helper::PHP_DATE_FORMAT, strtotime("+" . self::EXPIRE_IN . " days")));
        $notification->status = 'U';

        return $notificationRepo->add($notification);
    }

    public static function pushNotification(int $eventType, Model $model, AdapterInterface $adapter) {
        switch ($eventType) {
            case NotificationEvents::LEAVE_APPLIED:
                $leaveApply = $model;
                $recommdAppRepo = new RecommendApproveRepository($adapter);
                $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($leaveApply->employeeId);
                $route = ["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 2];
                self::addNotifications("Leave Applied", "Leave Request From " . $recommdAppModel['FIRST_NAME'], $recommdAppModel['EMPLOYEE_ID'], $recommdAppModel['RECOMMEND_BY'], json_encode($route), $adapter);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_ACCEPTED:
                $leaveApply = new LeaveApply();
                $route = ["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 2];
                self::addNotifications("Leave Applied", "Leave Request From " . $recommdAppModel['FIRST_NAME'], $recommdAppModel['EMPLOYEE_ID'], $recommdAppModel['RECOMMEND_BY'], json_encode($route), $adapter);

                break;
            case NotificationEvents::LEAVE_RECOMMEND_REJECTED:
                break;
            case NotificationEvents::LEAVE_APPROVE_ACCEPTED:
                break;
            case NotificationEvents::LEAVE_APPROVE_REJECTED:
                break;
        }
    }

}
