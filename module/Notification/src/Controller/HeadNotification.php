<?php

namespace Notification\Controller;

use Application\Helper\EmailHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Notification\Model\Notification;
use Notification\Model\NotificationEvents;
use Notification\Repository\NotificationRepo;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mail\Message;

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
        $notification->messageDateTime = Helper::getcurrentExpressionDateTime();
        $notification->expiryTime = Helper::getExpressionDate(date(Helper::PHP_DATE_FORMAT, strtotime("+" . self::EXPIRE_IN . " days")));
        $notification->status = 'U';
        return $notificationRepo->add($notification);
    }

    private static function sendEmail(int $from, int $to, int $type, AdapterInterface $adapter) {
        $emailTemplateRepo = new \Notification\Repository\EmailTemplateRepo($adapter);
        $template = $emailTemplateRepo->fetchById($type);

        $employeeRepo = new EmployeeRepository($adapter);
        $fromEmployee = $employeeRepo->fetchById($from);
        $toEmployee = $employeeRepo->fetchById($to);

        $mail = new Message();
        $mail->setSubject($template['SUBJECT']);
        $mail->setBody($template['DESCRIPTION']);
        $mail->setFrom('ukesh.gaiju@itnepal.com', $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME']);
        $mail->addTo('somkala.pachhai@itnepal.com', $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME']);

        $cc = (array) json_decode($template['CC']);
        foreach ($cc as $ccObj) {
            $ccObj = (array) $ccObj;
            $mail->addCc($ccObj['email'], $ccObj['name']);
        }

        $bcc = (array) json_decode($template['BCC']);
        foreach ($bcc as $bccObj) {
            $bccObj = (array) $bccObj;
            $mail->addBcc($bccObj['email'], $bccObj['name']);
        }

        EmailHelper::sendEmail($mail);
    }

    public static function pushNotification(int $eventType, Model $model, AdapterInterface $adapter) {
        switch ($eventType) {
            case NotificationEvents::LEAVE_APPLIED:
                $leaveApply = $model;
                $recommdAppRepo = new RecommendApproveRepository($adapter);
                $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($leaveApply->employeeId);
                $route = ["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 2];
                self::sendEmail($recommdAppModel['EMPLOYEE_ID'], $recommdAppModel['RECOMMEND_BY'], 1, $adapter);
                self::addNotifications("Leave Applied", "Leave Request From " . $recommdAppModel['FIRST_NAME'], $recommdAppModel['EMPLOYEE_ID'], $recommdAppModel['RECOMMEND_BY'], json_encode($route), $adapter);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_ACCEPTED:
                $leaveApply = $model;
                $route = ["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id];
                self::addNotifications("Leave Applied", "Leave Request Accepted ", $leaveApply->recommendedBy, $leaveApply->employeeId, json_encode($route), $adapter);

                $route = ["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 3];
                self::addNotifications("Leave Applied", "Leave Application approve request ", $leaveApply->employeeId, $leaveApply->approvedBy, json_encode($route), $adapter);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_REJECTED:
                $leaveApply = $model;
                $route = ["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id];
                self::addNotifications("Leave Applied", "Leave Request Rejected ", $leaveApply->recommendedBy, $leaveApply->employeeId, json_encode($route), $adapter);
                break;
            case NotificationEvents::LEAVE_APPROVE_ACCEPTED:
                $leaveApply = $model;
                $route = ["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id];
                self::addNotifications("Leave Applied", "Leave Request Approved ", $leaveApply->approvedBy, $leaveApply->employeeId, json_encode($route), $adapter);
                break;
            case NotificationEvents::LEAVE_APPROVE_REJECTED:
                $leaveApply = $model;
                $route = ["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id];
                self::addNotifications("Leave Applied", "Leave Request rejected on approval ", $leaveApply->approvedBy, $leaveApply->employeeId, json_encode($route), $adapter);
                break;
        }
    }

}
