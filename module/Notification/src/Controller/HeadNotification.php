<?php

namespace Notification\Controller;

use Application\Factory\HrLogger;
use Application\Helper\EmailHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Exception;
use Html2Text\Html2Text;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveApplyRepository;
use Notification\Model\LeaveRequestNotificationModel;
use Notification\Model\Notification;
use Notification\Model\NotificationEvents;
use Notification\Model\NotificationModel;
use Notification\Repository\NotificationRepo;
use SelfService\Model\AdvanceRequest;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Model\LoanRequest;
use SelfService\Model\TravelRequest;
use SelfService\Repository\AdvanceRequestRepository;
use SelfService\Repository\AttendanceRequestRepository;
use SelfService\Repository\LoanRequestRepository;
use SelfService\Repository\TravelRequestRepository;
use Setup\Model\RecommendApprove;
use Setup\Model\Training;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\TrainingRepository;
use Training\Model\TrainingAssign;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mail\Message;
use Zend\Mvc\Controller\Plugin\Url;
use SelfService\Model\WorkOnDayoff;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\WorkOnDayoffRepository;
use SelfService\Repository\WorkOnHolidayRepository;
use SelfService\Model\TrainingRequest;
use SelfService\Repository\TrainingRequestRepository;
use Notification\Model\WorkOnDayoffNotificationModel;
use Notification\Model\WorkOnHolidayNotificationModel;

class HeadNotification {

    const EXPIRE_IN = 14;

    private $adapter;

    const RECOMMENDER = 1;
    const APPROVER = 2;
    const ACCEPTED = "Accepted";
    const REJECTED = "Rejected";
    const ASSIGNED = "Assigned";
    const CANCELLED = "Cancelled";

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

    private static function sendEmail(NotificationModel $model, int $type, AdapterInterface $adapter, Url $url) {
        $isValidEmail = function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        };
        $emailTemplateRepo = new \Notification\Repository\EmailTemplateRepo($adapter);
        $template = $emailTemplateRepo->fetchById($type);

        if (null != $template) {
            $mail = new Message();
            $mail->setSubject($template['SUBJECT']);
            $htmlDescription = $model->processString($template['DESCRIPTION'], $url);
            $html2txt = new Html2Text($htmlDescription);
            $mail->setBody($html2txt->getText());

            if (!isset($model->fromEmail) || $model->fromEmail == null || $model->fromEmail == '' || !$isValidEmail($model->fromEmail)) {
                throw new Exception("Sender email is not set or valid.");
            }
            if (!isset($model->toEmail) || $model->toEmail == null || $model->toEmail == '' || !$isValidEmail($model->toEmail)) {
                throw new Exception("Receiver email is not set or valid.");
            }
            $mail->setFrom($model->fromEmail, $model->fromName);
            $mail->addTo($model->toEmail, $model->toName);

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
            HrLogger::getInstance()->info("Email Sent =>" . "From " . $model->fromEmail . " To " . $model->toEmail);
        } else {
            throw new Exception('email template not set.');
        }
    }

    public static function pushNotification(int $eventType, Model $model, AdapterInterface $adapter, Url $url) {
        ${"fn" . NotificationEvents::LEAVE_APPLIED} = function (LeaveApply $model, AdapterInterface $adapter, Url $url, $type) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($leaveApply->employeeId);

            if ($recommdAppModel == null) {
                throw new Exception("recommender and approver not set for employee with id =>" . $leaveApply->employeeId);
            }
            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $leaveReqNotiMod, $adapter);

            $leaveReqNotiMod->route = json_encode(["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";

            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 1, $adapter, $url);
        };
        ${"fn" . NotificationEvents::LEAVE_RECOMMEND_ACCEPTED} = function (LeaveApply $model, AdapterInterface $adapter, Url $url, string $status) {
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
            $leaveReqNotiMod->leaveRecommendStatus = $status;

            $notificationTitle = "Leave Request";
            $notificationDesc = "Recommendation of Leave Request by"
                    . " $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate"
                    . " to $leaveReqNotiMod->toDate is $leaveReqNotiMod->leaveRecommendStatus";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 2, $adapter, $url);
        };
        ${"fn" . NotificationEvents::LEAVE_APPROVE_ACCEPTED} = function (LeaveApply $model, AdapterInterface $adapter, Url $url, string $status) {
            $leaveApplyRepo = new LeaveApplyRepository($adapter);
            $leaveApplyArray = $leaveApplyRepo->fetchById($model->id)->getArrayCopy();
            $leaveApply = new LeaveApply();
            $leaveApply->exchangeArrayFromDB($leaveApplyArray);
            $leaveApply->approvedBy = $model->approvedBy;

            $leaveReqNotiMod = new LeaveRequestNotificationModel();
            self::setNotificationModel($leaveApply->approvedBy, $leaveApply->employeeId, $leaveReqNotiMod, $adapter);

            $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

            $leaveReqNotiMod->fromDate = $leaveApply->startDate;
            $leaveReqNotiMod->toDate = $leaveApply->endDate;
            $leaveReqNotiMod->leaveName = $leaveApply->leaveId;
            $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
            $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;
            $leaveReqNotiMod->leaveApprovedStatus = $status;

            $notificationTitle = "Leave Approval";
            $notificationDesc = "Approval of Leave Request by $leaveReqNotiMod->fromName from "
                    . "$leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate is $leaveReqNotiMod->leaveApprovedStatus";
            self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($leaveReqNotiMod, 3, $adapter, $url);
        };

        ${"fn" . NotificationEvents::ATTENDANCE_APPLIED} = function (AttendanceRequestModel $request, AdapterInterface $adapter, Url $url) {
            $attendReqRepo = new AttendanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($attendReqRepo->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AttendanceRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[RecommendApprove::RECOMMEND_BY], $notification, $adapter);
            $notification->attendanceDate = $request->attendanceDt;
            $notification->inTime = $request->inTime;
            $notification->outTime = $request->outTime;
            $notification->inRemarks = $request->inRemarks;
            $notification->outRemarks = $request->outRemarks;

            $notification->totalHours = $request->totalHour;
            $notification->route = json_encode(["route" => "attedanceapprove", "action" => "view", "id" => $request->id]);

            $title = "Attendance Request";
            $desc = "No description for now";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 4, $adapter, $url);
        };

        ${"fn" . NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED} = function (AttendanceRequestModel $request, AdapterInterface $adapter, Url $url, string $status) {
            $attendanceReqRepo = new AttendanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($attendanceReqRepo->fetchById($request->id));

            $notification = new \Notification\Model\AttendanceRequestNotificationModel();
            self::setNotificationModel($request->approvedBy, $request->employeeId, $notification, $adapter);

            $notification->attendanceDate = $request->attendanceDt;
            $notification->inTime = $request->inTime;
            $notification->outTime = $request->outTime;
            $notification->inRemarks = $request->inRemarks;
            $notification->outRemarks = $request->outRemarks;
            $notification->totalHours = $request->totalHour;
            $notification->status = $status;

            $notification->route = json_encode(["route" => "attendancerequest", "action" => "view", "id" => $request->id]);
            $title = "Attendance Request";
            $desc = "No description for now";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 5, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_APPLIED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url, $type) {

            $advanceRequestRepo = new AdvanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($advanceRequestRepo->fetchById($request->advanceRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AdvanceRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $notification, $adapter);

            $notification->advanceDate = $request->advanceDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->terms = $request->terms;

            $notification->route = json_encode(["route" => "advanceApprove", "action" => "view", "id" => $request->advanceRequestId, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $title = "Advance Request";
            $desc = "No description for now";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 6, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url) {
            $advanceRequestRepo = new AdvanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($advanceRequestRepo->fetchById($request->advanceRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AdvanceRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->advanceDate = $request->advanceDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->terms = $request->terms;
            $notification->status = "Accepted";

            $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
            $title = "Advance Recommend";
            $desc = "Advance Recommend is accepted";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 7, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_RECOMMEND_REJECTED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url) {
            $advanceRequestRepo = new AdvanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($advanceRequestRepo->fetchById($request->advanceRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AdvanceRequestNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::RECOMMEND_BY]
                    , $recommdAppModel[RecommendApprove::EMPLOYEE_ID]
                    , $notification, $adapter);

            $notification->advanceDate = $request->advanceDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->terms = $request->terms;
            $notification->status = "Rejected";

            $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
            $title = "Advance Recommend";
            $desc = "Advance Recommend is Rejected";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 7, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_APPROVE_ACCEPTED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url) {
            $advanceRequestRepo = new AdvanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($advanceRequestRepo->fetchById($request->advanceRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AdvanceRequestNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->advanceDate = $request->advanceDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->terms = $request->terms;
            $notification->status = "Accepted";

            $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
            $title = "Advance Approve";
            $desc = "Advance Approve is Accepted";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 8, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_APPROVE_REJECTED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url) {
            $advanceRequestRepo = new AdvanceRequestRepository($adapter);
            $request->exchangeArrayFromDB($advanceRequestRepo->fetchById($request->advanceRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\AdvanceRequestNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->advanceDate = $request->advanceDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->terms = $request->terms;
            $notification->status = "Accepted";

            $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
            $title = "Advance Approve";
            $desc = "Advance Approve is Rejected";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 8, $adapter, $url);
        };
        ${"fn" . NotificationEvents::ADVANCE_CANCELLED} = function (AdvanceRequest $request, AdapterInterface $adapter, Url $url) {
            
        };
        ${"fn" . NotificationEvents::TRAVEL_APPLIED} = function (TravelRequest $request, AdapterInterface $adapter, Url $url, $type) {
            $travelReqRepo = new TravelRequestRepository($adapter);
            $request->exchangeArrayFromDB($travelReqRepo->fetchById($request->travelId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\TravelReqNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $notification, $adapter);
            $notification->destination = $request->destination;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->purpose = $request->purpose;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->requestedType = $request->requestedType;

            $notification->route = json_encode(["route" => "travelApprove", "action" => "view", "id" => $request->travelId, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $title = "Travel Request";
            $desc = "Travel Request";


            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 9, $adapter, $url);
        };
        ${"fn" . NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED} = function (TravelRequest $request, AdapterInterface $adapter, Url $url, string $status) {
            $travelReqRepo = new TravelRequestRepository($adapter);
            $request->exchangeArrayFromDB($travelReqRepo->fetchById($request->travelId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\TravelReqNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->destination = $request->destination;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->purpose = $request->purpose;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->requestedType = $request->requestedType;

            $notification->status = $status;

            $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
            $title = "Travel Recommendation";
            $desc = "Travel Recommendation $status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 10, $adapter, $url);
        };
        ${"fn" . NotificationEvents::TRAVEL_APPROVE_ACCEPTED} = function (TravelRequest $request, AdapterInterface $adapter, Url $url, string $status) {
            $travelReqRepo = new TravelRequestRepository($adapter);
            $request->exchangeArrayFromDB($travelReqRepo->fetchById($request->travelId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\TravelReqNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->destination = $request->destination;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->purpose = $request->purpose;
            $notification->requestedAmount = $request->requestedAmount;
            $notification->requestedType = $request->requestedType;

            $notification->status = $status;

            $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
            $title = "Travel Approval";
            $desc = "Travel Approval $status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 11, $adapter, $url);
        };
        ${"fn" . NotificationEvents::TRAVEL_CANCELLED} = function (TravelRequest $request, AdapterInterface $adapter, Url $url) {
            
        };
        ${"fn" . NotificationEvents::TRAINING_ASSIGNED} = function (TrainingAssign $request, AdapterInterface $adapter, Url $url, $type) {

            $auth = new AuthenticationService();
            $assignerId = $auth->getStorage()->read()['employee_id'];

            $notification = new \Notification\Model\TrainingReqNotificationModel();
            self::setNotificationModel($assignerId, $request->employeeId, $notification, $adapter);

            $training = new Training();
            $trainingRepo = new TrainingRepository($adapter);
            $training->exchangeArrayFromDB($trainingRepo->fetchById($request->trainingId));

            $notification->duration = $training->duration;
            $notification->endDate = $training->endDate;
            $notification->startDate = $training->startDate;
            $notification->instuctorName = $training->instuctorName;
            $notification->trainingCode = $training->trainingCode;
            $notification->trainingName = $training->trainingName;
            $notification->trainingType = $training->trainingType;
            $notification->status = $type;


            $notification->route = json_encode(["route" => "trainingList", "action" => "view", "employeeId" => $request->employeeId, "trainingId" => $request->trainingId]);
            $title = "Training $type";
            $desc = "Training $type";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 12, $adapter, $url);
        };

        ${"fn" . NotificationEvents::LOAN_APPLIED} = function (LoanRequest $request, AdapterInterface $adapter, Url $url, $type) {
            $loanReqRepo = new LoanRequestRepository($adapter);
            $request->exchangeArrayFromDB($loanReqRepo->fetchById($request->loanRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\LoanRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $notification, $adapter);

            $notification->approvedAmount = $request->approvedAmount;
            $notification->deductOnSalary = $request->deductOnSalary;
            $notification->loanDate = $request->loanDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;

            $notification->route = json_encode(["route" => "loanApprove", "action" => "view", "id" => $request->loanRequestId, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $title = "Loan Request";
            $desc = "Loan Request";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 13, $adapter, $url);
        };
        ${"fn" . NotificationEvents::LOAN_RECOMMEND_ACCEPTED} = function (LoanRequest $request, AdapterInterface $adapter, Url $url, string $status) {
            $loanReqRepo = new LoanRequestRepository($adapter);
            $request->exchangeArrayFromDB($loanReqRepo->fetchById($request->loanRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\LoanRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->approvedAmount = $request->approvedAmount;
            $notification->deductOnSalary = $request->deductOnSalary;
            $notification->loanDate = $request->loanDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;

            $notification->status = $status;

            $notification->route = json_encode(["route" => "loanRequest", "action" => "view", "id" => $request->loanRequestId]);
            $title = "Loan Recommend";
            $desc = "Loan Recommend $status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 14, $adapter, $url);
        };
        ${"fn" . NotificationEvents::LOAN_APPROVE_ACCEPTED} = function (LoanRequest $request, AdapterInterface $adapter, Url $url, string $status) {
            $loanReqRepo = new LoanRequestRepository($adapter);
            $request->exchangeArrayFromDB($loanReqRepo->fetchById($request->loanRequestId));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new \Notification\Model\LoanRequestNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->approvedAmount = $request->approvedAmount;
            $notification->deductOnSalary = $request->deductOnSalary;
            $notification->loanDate = $request->loanDate;
            $notification->reason = $request->reason;
            $notification->requestedAmount = $request->requestedAmount;

            $notification->status = $status;

            $notification->route = json_encode(["route" => "loanRequest", "action" => "view", "id" => $request->loanRequestId]);
            $title = "Loan Approval";
            $desc = "Loan Approval $status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 15, $adapter, $url);
        };
        ${"fn" . NotificationEvents::WORKONDAYOFF_APPLIED} = function (WorkOnDayoff $model, AdapterInterface $adapter, Url $url, $type) {
            $workOnDayoffRepo = new WorkOnDayoffRepository($adapter);
            $workOnDayoffArray = $workOnDayoffRepo->fetchById($model->id);
            $workOnDayoff = new WorkOnDayoff();
            $workOnDayoff->exchangeArrayFromDB($workOnDayoffArray);

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($workOnDayoff->employeeId);

            if ($recommdAppModel == null) {
                throw new Exception("recommender and approver not set for employee with id =>" . $workOnDayoff->employeeId);
            }
            $workOnDayoffReqNotiMod = new WorkOnDayoffNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $workOnDayoffReqNotiMod, $adapter);

            $workOnDayoffReqNotiMod->route = json_encode(["route" => "dayoffWorkApprove", "action" => "view", "id" => $workOnDayoff->id, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $workOnDayoffReqNotiMod->fromDate = $workOnDayoff->fromDate;
            $workOnDayoffReqNotiMod->toDate = $workOnDayoff->toDate;
            $workOnDayoffReqNotiMod->duration = $workOnDayoff->duration;
            $workOnDayoffReqNotiMod->remarks = $workOnDayoff->remarks;

            $notificationTitle = "Work On Day-off Request";
            $notificationDesc = "Work On Day-off Request of $workOnDayoffReqNotiMod->fromName from $workOnDayoffReqNotiMod->fromDate to $workOnDayoffReqNotiMod->toDate";

            self::addNotifications($workOnDayoffReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
            self::sendEmail($workOnDayoffReqNotiMod, 16, $adapter, $url);
        };
        ${"fn" . NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED} = function (WorkOnDayoff $request, AdapterInterface $adapter, Url $url, string $status) {
            $workOnDayoffRepo = new WorkOnDayoffRepository($adapter);
            $request->exchangeArrayFromDB($workOnDayoffRepo->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new WorkOnDayoffNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->duration = $request->duration;
            $notification->remarks = $request->remarks;
            $notification->status =  $status;

            $notification->route = json_encode(["route" => "workOnDayoff", "action" => "view", "id" => $request->id]);
            $title = "Work On Day-off Recommendation";
            $desc = "Recommendation of Work on Day-off Request by"
                    . " $notification->fromName from $notification->fromDate"
                    . " to $notification->toDate is $notification->status";
            
            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 17, $adapter, $url);
        };
        ${"fn" . NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED} = function (WorkOnDayoff $request, AdapterInterface $adapter, Url $url, string $status) {
            $workOnDayoffRepo = new WorkOnDayoffRepository($adapter);
            $request->exchangeArrayFromDB($workOnDayoffRepo->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new WorkOnDayoffNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->duration = $request->duration;
            $notification->remarks = $request->remarks;
            $notification->status =  $status;

            $notification->route = json_encode(["route" => "workOnDayoff", "action" => "view", "id" => $request->id]);
            $title = "Work On Day-off Approval";
            $desc = "Approval of Work on Day-off Request by"
                    . " $notification->fromName from $notification->fromDate"
                    . " to $notification->toDate is $notification->status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 18, $adapter, $url);
        };
        
        ${"fn" . NotificationEvents::WORKONHOLIDAY_APPLIED} = function (WorkOnHoliday $request, AdapterInterface $adapter, Url $url, $type) {
            $workOnHolidayRep = new WorkOnHolidayRepository($adapter);
            $request->exchangeArrayFromDB($workOnHolidayRep->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            if ($recommdAppModel == null) {
                throw new Exception("recommender and approver not set for employee with id =>" . $request->employeeId);
            }
            $notification = new WorkOnHolidayNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $recommdAppModel[($type == self::RECOMMENDER) ? RecommendApprove::RECOMMEND_BY : RecommendApprove::APPROVED_BY], $notification, $adapter);

            $notification->route = json_encode(["route" => "holidayWorkApprove", "action" => "view", "id" => $request->id, "role" => ($type == self::RECOMMENDER) ? 2 : 3]);
            $notification->holidayName = $request->holidayId;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->duration = $request->duration;
            $notification->remarks = $request->remarks;

            $title = "Work On Holiday Request";
            $desc = "Work On Holiday Request of $notification->fromName from $notification->fromDate to $notification->toDate";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 19, $adapter, $url);
        };
        ${"fn" . NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED} = function (WorkOnHoliday $request, AdapterInterface $adapter, Url $url, string $status) {
            $workOnHolidayRepo = new WorkOnHolidayRepository($adapter);
            $request->exchangeArrayFromDB($workOnHolidayRepo->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new WorkOnHolidayNotificationModel();
            self::setNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->holidayName = $request->holidayId;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->duration = $request->duration;
            $notification->remarks = $request->remarks;
            $notification->status =  $status;

            $notification->route = json_encode(["route" => "workOnHoliday", "action" => "view", "id" => $request->id]);
            $title = "Work On Holiday Recommendation";
            $desc = "Recommendation of Work on Holiday Request by"
                    . " $notification->fromName from $notification->fromDate"
                    . " to $notification->toDate is $notification->status";
            
            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 20, $adapter, $url);
        };
        ${"fn" . NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED} = function (WorkOnHoliday $request, AdapterInterface $adapter, Url $url, string $status) {
            $workOnHolidayRepo = new WorkOnHolidayRepository($adapter);
            $request->exchangeArrayFromDB($workOnHolidayRepo->fetchById($request->id));

            $recommdAppRepo = new RecommendApproveRepository($adapter);
            $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($request->employeeId);

            $notification = new WorkOnHolidayNotificationModel();
            self::setNotificationModel(
                    $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], $notification, $adapter);

            $notification->holidayName = $request->holidayId;
            $notification->fromDate = $request->fromDate;
            $notification->toDate = $request->toDate;
            $notification->duration = $request->duration;
            $notification->remarks = $request->remarks;
            $notification->status =  $status;

            $notification->route = json_encode(["route" => "workOnHoliday", "action" => "view", "id" => $request->id]);
            $title = "Work On Holiday Approval";
            $desc = "Approval of Work on Holiday Request by"
                    . " $notification->fromName from $notification->fromDate"
                    . " to $notification->toDate is $notification->status";

            self::addNotifications($notification, $title, $desc, $adapter);
            self::sendEmail($notification, 21, $adapter, $url);
        };
        
        switch ($eventType) {
            case NotificationEvents::LEAVE_APPLIED:
                ${"fn" . NotificationEvents::LEAVE_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::LEAVE_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                ${"fn" . NotificationEvents::LEAVE_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::LEAVE_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::LEAVE_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::LEAVE_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::LEAVE_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::LEAVE_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::ATTENDANCE_APPLIED:
                ${"fn" . NotificationEvents::ATTENDANCE_APPLIED}($model, $adapter, $url);
                break;
            case NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::ATTENDANCE_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::ADVANCE_APPLIED:
                ${"fn" . NotificationEvents::ADVANCE_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED}($model, $adapter, $url);
                ${"fn" . NotificationEvents::ADVANCE_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::ADVANCE_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::ADVANCE_APPROVE_REJECTED}($model, $adapter, $url);
                break;
            case NotificationEvents::ADVANCE_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::ADVANCE_APPROVE_ACCEPTED}($model, $adapter, $url);
                break;
            case NotificationEvents::ADVANCE_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::ADVANCE_APPROVE_REJECTED}($model, $adapter, $url);
                break;
            case NotificationEvents::ADVANCE_CANCELLED:
                ${"fn" . NotificationEvents::ADVANCE_CANCELLED}($model, $adapter, $url);
                break;
            case NotificationEvents::TRAVEL_APPLIED:
                ${"fn" . NotificationEvents::TRAVEL_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                ${"fn" . NotificationEvents::TRAVEL_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::TRAVEL_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::TRAVEL_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::TRAVEL_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::TRAVEL_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::TRAVEL_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::TRAVEL_CANCELLED:
                ${"fn" . NotificationEvents::TRAVEL_CANCELLED}($model, $adapter, $url);
                break;
            case NotificationEvents::TRAINING_ASSIGNED:
                ${"fn" . NotificationEvents::TRAINING_ASSIGNED}($model, $adapter, $url, self::ASSIGNED);
                break;
            case NotificationEvents::TRAINING_CANCELLED:
                ${"fn" . NotificationEvents::TRAINING_ASSIGNED}($model, $adapter, $url, self::CANCELLED);
                break;
            case NotificationEvents::LOAN_APPLIED:
                ${"fn" . NotificationEvents::LOAN_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::LOAN_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::LOAN_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                ${"fn" . NotificationEvents::LOAN_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::LOAN_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::LOAN_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::LOAN_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::LOAN_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::LOAN_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::LOAN_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPLIED:
                ${"fn" . NotificationEvents::WORKONDAYOFF_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                ${"fn" . NotificationEvents::WORKONDAYOFF_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::WORKONDAYOFF_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPLIED:
                ${"fn" . NotificationEvents::WORKONHOLIDAY_APPLIED}($model, $adapter, $url, self::RECOMMENDER);
                break;
            case NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED:
                ${"fn" . NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                ${"fn" . NotificationEvents::WORKONHOLIDAY_APPLIED}($model, $adapter, $url, self::APPROVER);
                break;
            case NotificationEvents::WORKONHOLIDAY_RECOMMEND_REJECTED:
                ${"fn" . NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED:
                ${"fn" . NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED}($model, $adapter, $url, self::ACCEPTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED:
                ${"fn" . NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED}($model, $adapter, $url, self::REJECTED);
                break;
        }
    }

    private static function setNotificationModel($fromId, $toId, NotificationModel &$notification, AdapterInterface $adapter) {
        $employeeRepo = new EmployeeRepository($adapter);
        $fromEmployee = $employeeRepo->fetchById($fromId);
        $toEmployee = $employeeRepo->fetchById($toId);

        $notification->fromId = $fromEmployee['EMPLOYEE_ID'];
        $notification->fromName = $fromEmployee['FIRST_NAME'] . " " . $fromEmployee['MIDDLE_NAME'] . " " . $fromEmployee['LAST_NAME'];
        $notification->fromEmail = $fromEmployee['EMAIL_OFFICIAL'];
        $notification->fromGender = $fromEmployee['GENDER_ID'];
        $notification->fromMaritualStatus = $fromEmployee['MARITAL_STATUS'];
        $notification->toEmail = $toEmployee['EMAIL_OFFICIAL'];
        $notification->toGender = $toEmployee['GENDER_ID'];
        $notification->toId = $toEmployee['EMPLOYEE_ID'];
        $notification->toMaritualStatus = $toEmployee['MARITAL_STATUS'];
        $notification->toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];
        $notification->setHonorific();
    }

}
