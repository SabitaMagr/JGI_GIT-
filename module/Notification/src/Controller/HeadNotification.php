<?php

namespace Notification\Controller;

use Application\Helper\EmailHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveApplyRepository;
use Notification\Model\LeaveRequestNotificationModel;
use Notification\Model\Notification;
use Notification\Model\NotificationEvents;
use Notification\Model\NotificationModel;
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

    private static function addNotifications(NotificationModel $notiModel, string $title, string $desc, AdapterInterface $adapter) {
        $notificationRepo = new NotificationRepo($adapter);
        $notification = new Notification();
        $notification->messageTitle = $title;
        $notification->messageDesc = $desc;
        $notification->messageFrom = $notiModel->fromId;
        $notification->messageTo = $notiModel->toId;
        $notification->route = $notiModel->route;
        $notification->messageId = ((int) Helper::getMaxId($adapter, Notification::TABLE_NAME, Notification::MESSAGE_ID)) + 1;
        $notification->messageDateTime = Helper::getcurrentExpressionDateTime();
        $notification->expiryTime = Helper::getExpressionDate(date(Helper::PHP_DATE_FORMAT, strtotime("+" . self::EXPIRE_IN . " days")));
        $notification->status = 'U';
        return $notificationRepo->add($notification);
    }

    private static function sendEmail(NotificationModel $model, int $type, AdapterInterface $adapter) {
        $emailTemplateRepo = new \Notification\Repository\EmailTemplateRepo($adapter);
        $template = $emailTemplateRepo->fetchById($type);

        $mail = new Message();
        $mail->setSubject($template['SUBJECT']);
        $mail->setBody($template['DESCRIPTION']);
        $mail->setFrom('ukesh.gaiju@itnepal.com', $model->fromName);
        $mail->addTo('somkala.pachhai@itnepal.com', $model->toName);

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
        ${"fn" . NotificationEvents::LEAVE_APPLIED} = function(LeaveApply $model, AdapterInterface $adapter) {
            $leaveApply = $model;
            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($leaveApply->employeeId);

            $employeeRepo = new EmployeeRepository($adapter);
            $fromEmployee = $employeeRepo->fetchById($recommdAppModel['EMPLOYEE_ID']);
            $toEmployee = $employeeRepo->fetchById($recommdAppModel['RECOMMEND_BY']);

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            $leaveReqNotiMod->fromId = $recommdAppModel['EMPLOYEE_ID'];
            $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
            $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
            $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
            $leaveReqNotiMod->toId = $recommdAppModel['RECOMMEND_BY'];
            $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
            $leaveReqNotiMod->route = json_encode(["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 2]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate->getExpression();
            $leaveReqNotiMod->toDate = $leaveApply->endDate->getExpression();
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";

            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 1, $adapter);
        };

        ${"fn" . NotificationEvents::LEAVE_RECOMMEND_ACCEPTED} = function(LeaveApply $model, AdapterInterface $adapter) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);
            $leaveApply->approvedBy = $model->approvedBy;

            $employeeRepo = new EmployeeRepository($adapter);
            $fromEmployee = $employeeRepo->fetchById($leaveApply->recommendedBy);
            $toEmployee = $employeeRepo->fetchById($leaveApply->employeeId);

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            $leaveReqNotiMod->fromId = $leaveApply->recommendedBy;
            $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
            $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
            $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
            $leaveReqNotiMod->toId = $leaveApply->employeeId;
            $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
            $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Recommendation of Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 2, $adapter);
            $temp = function() use($employeeRepo, $leaveApply, $adapter) {
                $fromEmployee = $employeeRepo->fetchById($leaveApply->employeeId);
                $toEmployee = $employeeRepo->fetchById($leaveApply->approvedBy);

                $leaveReqNotiMod = new LeaveRequestNotificationModel();
                $leaveReqNotiMod->fromId = $leaveApply->employeeId;
                $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
                $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
                $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
                $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
                $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
                $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
                $leaveReqNotiMod->toId = $leaveApply->approvedBy;
                $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
                $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
                $leaveReqNotiMod->route = json_encode(["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => 3]);

                $leaveReqNotiMod->fromDate = $leaveApply->startDate;
                $leaveReqNotiMod->toDate = $leaveApply->endDate;
                $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
                $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
                $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

                $notificationTitle = "Leave Request";
                $notificationDesc = "Recommendation of Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";

                self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
                self::sendEmail($leaveReqNotiMod, 2, $adapter);
            };
            $temp();
        };
        ${"fn" . NotificationEvents::LEAVE_RECOMMEND_REJECTED} = function(LeaveApply $model, AdapterInterface $adapter) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);
            $leaveApply->approvedBy = $model->approvedBy;

            $employeeRepo = new EmployeeRepository($adapter);
            $fromEmployee = $employeeRepo->fetchById($leaveApply->recommendedBy);
            $toEmployee = $employeeRepo->fetchById($leaveApply->employeeId);

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            $leaveReqNotiMod->fromId = $leaveApply->recommendedBy;
            $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
            $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
            $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
            $leaveReqNotiMod->toId = $leaveApply->employeeId;
            $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
            $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Recommendation of Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 2, $adapter);
        };
        ${"fn" . NotificationEvents::LEAVE_APPROVE_ACCEPTED} = function(LeaveApply $model, AdapterInterface $adapter) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);
            $leaveApply->approvedBy = $model->approvedBy;

            $employeeRepo = new EmployeeRepository($adapter);
            $fromEmployee = $employeeRepo->fetchById($leaveApply->approvedBy);
            $toEmployee = $employeeRepo->fetchById($leaveApply->employeeId);

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            $leaveReqNotiMod->fromId = $leaveApply->approvedBy;
            $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
            $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
            $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
            $leaveReqNotiMod->toId = $leaveApply->employeeId;
            $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
            $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Recommendation of Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 2, $adapter);
        };
        ${"fn" . NotificationEvents::LEAVE_APPROVE_REJECTED} = function(LeaveApply $model, AdapterInterface $adapter) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);
            $leaveApply->approvedBy = $model->approvedBy;

            $employeeRepo = new EmployeeRepository($adapter);
            $fromEmployee = $employeeRepo->fetchById($leaveApply->approvedBy);
            $toEmployee = $employeeRepo->fetchById($leaveApply->employeeId);

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            $leaveReqNotiMod->fromId = $leaveApply->approvedBy;
            $leaveReqNotiMod->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
            $leaveReqNotiMod->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->fromGender = $fromEmployee['GENDER_ID'];
            $leaveReqNotiMod->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toEmail = $toEmployee['EMAIL_OFFICIAL'];
            $leaveReqNotiMod->toGender = $toEmployee['GENDER_ID'];
            $leaveReqNotiMod->toId = $leaveApply->employeeId;
            $leaveReqNotiMod->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
            $leaveReqNotiMod->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
            $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Recommendation of Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 2, $adapter);
        };

        switch ($eventType) {
            case NotificationEvents::LEAVE_APPLIED:
                ${"fn" . NotificationEvents::LEAVE_APPLIED}($model, $adapter);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::LEAVE_RECOMMEND_ACCEPTED}($model, $adapter);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::LEAVE_RECOMMEND_REJECTED}($model, $adapter);
                break;
            case NotificationEvents::LEAVE_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::LEAVE_APPROVE_ACCEPTED}($model, $adapter);
                break;
            case NotificationEvents::LEAVE_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::LEAVE_APPROVE_REJECTED}($model, $adapter);
                break;
        }
    }

}
