<?php

namespace Notification\Controller;

use Application\Helper\EmailHelper;
use Application\Helper\Helper;
use Application\Model\ForgotPassword;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\AppraisalAssign;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Repository\AppraisalAssignRepository;
use Exception;
use HolidayManagement\Repository\HolidayRepository;
use Html2Text\Html2Text;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveApplyRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Model\SalaryDetail;
use ManagerService\Repository\LeaveApproveRepository;
use ManagerService\Repository\SalaryDetailRepo;
use Notification\Model\AppraisalNotificationModel;
use Notification\Model\LeaveRequestNotificationModel;
use Notification\Model\LeaveSubNotificationModel;
use Notification\Model\Notification;
use Notification\Model\NotificationEvents;
use Notification\Model\NotificationModel;
use Notification\Model\SalaryReviewNotificationModel;
use Notification\Model\TrainingReqNotificationModel;
use Notification\Model\TravelSubNotificationModel;
use Notification\Model\WorkOnDayoffNotificationModel;
use Notification\Model\WorkOnHolidayNotificationModel;
use Notification\Repository\NotificationRepo;
use SelfService\Model\AdvanceRequest;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Model\LoanRequest;
use SelfService\Model\Overtime;
use SelfService\Model\TrainingRequest;
use SelfService\Model\TravelRequest;
use SelfService\Model\WorkOnDayoff;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\AdvanceRequestRepository;
use SelfService\Repository\AttendanceRequestRepository;
use SelfService\Repository\LeaveSubstituteRepository;
use SelfService\Repository\LoanRequestRepository;
use SelfService\Repository\OvertimeRepository;
use SelfService\Repository\TrainingRequestRepository;
use SelfService\Repository\TravelRequestRepository;
use SelfService\Repository\TravelSubstituteRepository;
use SelfService\Repository\WorkOnDayoffRepository;
use SelfService\Repository\WorkOnHolidayRepository;
use Setup\Model\HrEmployees;
use Setup\Model\RecommendApprove;
use Setup\Model\Training;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\TrainingRepository;
use Training\Model\TrainingAssign;
use Travel\Repository\RecommenderApproverRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mail\Message;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\Url;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class HeadNotification {

    const EXPIRE_IN = 14;

    private $adapter;

    const RECOMMENDER = 1;
    const APPROVER = 2;
    const ACCEPTED = "Accepted";
    const REJECTED = "Rejected";
    const ASSIGNED = "Assigned";
    const CANCELLED = "Cancelled";
    const REVIEWER_EVALUATION = "REVIEWER_EVALUATION";
    const SUPER_REVIEWER_EVALUATION = "SUPER_REVIEWER_EVALUATION";
    const HR_FEEDBACK = "HR_FEEDBACK";
    const TRAVEL_EXPENSE_REQUEST = "ep";    //value from travel request form
    const TRAVEL_ADVANCE_REQUEST = "ad";

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

    private static function sendEmail(NotificationModel $model, int $type, AdapterInterface $adapter, AbstractActionController $context) {
//        return;
        $url = $context->plugin('url');
        $isValidEmail = function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        };
        $emailTemplateRepo = new \Notification\Repository\EmailTemplateRepo($adapter);
        $template = $emailTemplateRepo->fetchById($type);

        if (null == $template) {
            throw new Exception('Email template not set.');
        }
        
        $mail = new Message();
        $mail->setSubject($template['SUBJECT']);
        $htmlDescription = self::mailHeader($context);
        $htmlDescription .= $model->processString($template['DESCRIPTION'], $url);
        $htmlDescription .= self::mailFooter($context);
//        $html2txt = new Html2Text($htmlDescription);
//        $mail->setBody(self::mailHeader($context));         
    
        $htmlPart = new MimePart($htmlDescription);
        $htmlPart->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array($htmlPart));
        
        $mail->setBody($body);

        if (!isset($model->fromEmail) || $model->fromEmail == null || $model->fromEmail == '' || !$isValidEmail($model->fromEmail)) {
            throw new Exception("Sender email is not set or valid.");
        }
        if (!isset($model->toEmail) || $model->toEmail == null || $model->toEmail == '' || !$isValidEmail($model->toEmail)) {
            throw new Exception("Receiver email is not set or valid.");
        }
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
    }

    public static function getName($id, $repo, $name) {
        $detail = $repo->fetchById($id);
        return $detail[$name];
    }

    private static function initFullModel(RepositoryInterface $repository, Model &$model, $id) {
        $dbModel = $repository->fetchById($id);
        $data = null;

        if (gettype($dbModel) === "array") {
            $data = $dbModel;
        } else {
            $data = $dbModel->getArrayCopy();
        }
        $model->exchangeArrayFromDB($data);
    }

    private static function leaveApplied(LeaveApply $leaveApply, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new LeaveApplyRepository($adapter), $leaveApply, $leaveApply->id);
        $recommdAppModel = self::findRecApp($leaveApply->employeeId, $adapter);
        
        $leaveApproveRepository = new LeaveApproveRepository($adapter);
        $empRepository = new EmployeeRepository($adapter);
        $detail = $leaveApproveRepository->fetchById($leaveApply->id);
        $CEOFlag = ($detail['PAID']=='N' && $detail['NO_OF_DAYS']>3)?true:false;
        if($CEOFlag){
            $CEODtl = $empRepository->fetchByCondition([HrEmployees::STATUS=>'E', HrEmployees::IS_CEO=>'Y', HrEmployees::RETIRED_FLAG=>'N']);
            $recommdAppModel['RECOMMEND_BY']=$recommdAppModel['APPROVED_BY'];
            $recommdAppModel['APPROVED_BY'] = $CEODtl['EMPLOYEE_ID'];
        }
        
        $idAndRole = self::findRoleType($recommdAppModel, $type);
        $leaveReqNotiMod = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $idAndRole['id'], LeaveRequestNotificationModel::class, $adapter);

        $leaveName = self::getName($leaveApply->leaveId, new LeaveMasterRepository($adapter), 'LEAVE_ENAME');

        $leaveReqNotiMod->fromDate = $leaveApply->startDate;
        $leaveReqNotiMod->toDate = $leaveApply->endDate;
        $leaveReqNotiMod->leaveName = $leaveName;
        $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
        $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;

        $leaveReqNotiMod->route = json_encode(["route" => "leaveapprove", "action" => "view", "id" => $leaveApply->id, "role" => $idAndRole['role']]);
        
        $notificationTitle = "Leave Request";
        $notificationDesc = "Leave Request of $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate";

        self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
        self::sendEmail($leaveReqNotiMod, 1, $adapter, $context);
    }

    private static function leaveRecommend(LeaveApply $leaveApply, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new LeaveApplyRepository($adapter), $leaveApply, $leaveApply->id);
        $recommendAppModel = self::findRecApp($leaveApply->employeeId, $adapter);
        $leaveApproveRepository = new LeaveApproveRepository($adapter);
        $empRepository = new EmployeeRepository($adapter);
        $detail = $leaveApproveRepository->fetchById($leaveApply->id);
        $CEOFlag = ($detail['PAID']=='N' && $detail['NO_OF_DAYS']>3)?true:false;
        if($CEOFlag){
            $CEODtl = $empRepository->fetchByCondition([HrEmployees::STATUS=>'E', HrEmployees::IS_CEO=>'Y', HrEmployees::RETIRED_FLAG=>'N']);
            $recommendAppModel['RECOMMEND_BY']=$recommendAppModel['APPROVED_BY'];
            $recommendAppModel['APPROVED_BY'] = $CEODtl['EMPLOYEE_ID'];
        }
        $leaveReqNotiMod = self::initializeNotificationModel($recommendAppModel[RecommendApprove::RECOMMEND_BY],$leaveApply->employeeId,  LeaveRequestNotificationModel::class, $adapter);
        
//
        $leaveReqNotiMod->fromDate = $leaveApply->startDate;
        $leaveReqNotiMod->toDate = $leaveApply->endDate;
        $leaveReqNotiMod->leaveName = self::getName($leaveApply->leaveId, new LeaveMasterRepository($adapter), 'LEAVE_ENAME');
        $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
        $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;
        $leaveReqNotiMod->remarks = $leaveApply->remarks;
        $leaveReqNotiMod->leaveRecommendStatus = $status;
        $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);
//
        $notificationTitle = "Leave Request";
        $notificationDesc = "Recommendation of Leave Request by"
                . " $leaveReqNotiMod->fromName from $leaveReqNotiMod->fromDate"
                . " to $leaveReqNotiMod->toDate is $leaveReqNotiMod->leaveRecommendStatus";
        self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
        self::sendEmail($leaveReqNotiMod, 2, $adapter, $context);
    }

    public static function leaveApprove(LeaveApply $leaveApply, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new LeaveApplyRepository($adapter), $leaveApply, $leaveApply->id);
        $recommendAppModel = self::findRecApp($leaveApply->employeeId, $adapter);
        $leaveApproveRepository = new LeaveApproveRepository($adapter);
        $empRepository = new EmployeeRepository($adapter);
        $detail = $leaveApproveRepository->fetchById($leaveApply->id);
        $CEOFlag = ($detail['PAID']=='N' && $detail['NO_OF_DAYS']>3)?true:false;
        if($CEOFlag){
            $CEODtl = $empRepository->fetchByCondition([HrEmployees::STATUS=>'E', HrEmployees::IS_CEO=>'Y', HrEmployees::RETIRED_FLAG=>'N']);
            $recommendAppModel['RECOMMEND_BY']=$recommendAppModel['APPROVED_BY'];
            $recommendAppModel['APPROVED_BY'] = $CEODtl['EMPLOYEE_ID'];
        }
        $leaveReqNotiMod = self::initializeNotificationModel($recommendAppModel[RecommendApprove::APPROVED_BY], $leaveApply->employeeId, LeaveRequestNotificationModel::class, $adapter);


        $leaveReqNotiMod->fromDate = $leaveApply->startDate;
        $leaveReqNotiMod->toDate = $leaveApply->endDate;
        $leaveReqNotiMod->leaveName = self::getName($leaveApply->leaveId, new LeaveMasterRepository($adapter), 'LEAVE_ENAME');
        $leaveReqNotiMod->leaveType = $leaveApply->halfDay;
        $leaveReqNotiMod->noOfDays = $leaveApply->noOfDays;
        $leaveReqNotiMod->leaveApprovedStatus = $status;

        $leaveReqNotiMod->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $leaveApply->id]);

        $notificationTitle = "Leave Approval";
        $notificationDesc = "Approval of Leave Request by $leaveReqNotiMod->fromName from "
                . "$leaveReqNotiMod->fromDate to $leaveReqNotiMod->toDate is $leaveReqNotiMod->leaveApprovedStatus";
        self::addNotifications($leaveReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
        self::sendEmail($leaveReqNotiMod, 3, $adapter, $context);
    }

    public static function attendanceRequest(AttendanceRequestModel $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new AttendanceRequestRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $idAndRole = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $idAndRole['id'], \Notification\Model\AttendanceRequestNotificationModel::class, $adapter);

        $notification->attendanceDate = $request->attendanceDt;
        $notification->inTime = $request->inTime;
        $notification->outTime = $request->outTime;
        $notification->inRemarks = $request->inRemarks;
        $notification->outRemarks = $request->outRemarks;

        $notification->totalHours = $request->totalHour;
        $notification->route = json_encode(["route" => "attedanceapprove", "action" => "view", "id" => $request->id, "role" => $idAndRole['role']]);

        $title = "Attendance Request";
        $desc = "Attendance Request Applied";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 4, $adapter, $context);
    }

    public static function attendanceRecommend(AttendanceRequestModel $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new AttendanceRequestRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\AdvanceRequestNotificationModel::class, $adapter);

        $notification->attendanceDate = $request->attendanceDt;
        $notification->inTime = $request->inTime;
        $notification->outTime = $request->outTime;
        $notification->inRemarks = $request->inRemarks;
        $notification->outRemarks = $request->outRemarks;
        $notification->totalHours = $request->totalHour;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "attendancerequest", "action" => "view", "id" => $request->id]);

        $title = "Attendance Request";
        $desc = "Attendance Request is " . $status;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 5, $adapter, $context);
    }

    public static function attendanceApprove(AttendanceRequestModel $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new AttendanceRequestRepository($adapter), $request, $request->id);
        $recApp = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recApp[AttendanceRequestModel::APPROVED_BY], $request->employeeId, \Notification\Model\AttendanceRequestNotificationModel::class, $adapter);

        $notification->attendanceDate = $request->attendanceDt;
        $notification->inTime = $request->inTime;
        $notification->outTime = $request->outTime;
        $notification->inRemarks = $request->inRemarks;
        $notification->outRemarks = $request->outRemarks;
        $notification->totalHours = $request->totalHour;
        $notification->status = $status;

        $title = "Attendance Request";
        $desc = "Attendance Request " . $status;

        $notification->route = json_encode(["route" => "attendancerequest", "action" => "view", "id" => $request->id]);

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 5, $adapter, $context);
    }

    public static function advanceApplied(AdvanceRequest $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new AdvanceRequestRepository($adapter), $request, $request->advanceRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], \Notification\Model\AdvanceRequestNotificationModel::class, $adapter);

        $notification->advanceDate = $request->advanceDate;
        $notification->reason = $request->reason;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->terms = $request->terms;

        $notification->route = json_encode(["route" => "advanceApprove", "action" => "view", "id" => $request->advanceRequestId, "role" => $roleAndId['role']]);
        $title = "Advance Request";
        $desc = "No description for now";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 6, $adapter, $context);
    }

    public static function advanceRecommend(AdvanceRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new AdvanceRequestRepository($adapter), $request, $request->advanceRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\AdvanceRequestNotificationModel::class, $adapter);

        $notification->advanceDate = $request->advanceDate;
        $notification->reason = $request->reason;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->terms = $request->terms;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
        $title = "Advance Recommend";
        $desc = "Advance Recommend is {$status}";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 7, $adapter, $context);
    }

    private static function advanceApprove(AdvanceRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new AdvanceRequestRepository($adapter), $request, $request->advanceRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\AdvanceRequestNotificationModel::class, $adapter);

        $notification->advanceDate = $request->advanceDate;
        $notification->reason = $request->reason;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->terms = $request->terms;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "advanceRequest", "action" => "view", "id" => $request->advanceRequestId]);
        $title = "Advance Approve";
        $desc = "Advance Approve is {$status}";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 8, $adapter, $context);
    }

    private static function travelApplied(TravelRequest $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new TravelRequestRepository($adapter), $request, $request->travelId);
        $recommdAppModel = self::findRecAppForTrvl($request->employeeId, $adapter,$request->approverRole);
        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], \Notification\Model\TravelReqNotificationModel::class, $adapter);


        $notification->destination = $request->destination;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->purpose = $request->purpose;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->requestedType = $request->requestedType;
        
        switch($request->requestedType){
            case self::TRAVEL_ADVANCE_REQUEST:
                $notification->route = json_encode(["route" => "travelApprove", "action" => "view", "id" => $request->travelId, "role" => $roleAndId['role']]);
                break;
            case self::TRAVEL_EXPENSE_REQUEST :
                $notification->route = json_encode(["route" => "travelApprove", "action" => "expenseDetail", "id" => $request->travelId, "role" => $roleAndId['role']]);
                break;
            default:
                $notification->route = json_encode(["route" => "travelApprove", "action" => "view", "id" => $request->travelId, "role" => $roleAndId['role']]);
                break;
        }
        $title = "Travel Request";
        $desc = "Travel Request of $notification->fromName from $notification->fromDate to $notification->toDate";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 9, $adapter, $context);
    }

    private static function travelRecommend(TravelRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new TravelRequestRepository($adapter), $request, $request->travelId);
        $recommdAppModel = self::findRecAppForTrvl($request->employeeId, $adapter,$request->approverRole);
        $notification = self::initializeNotificationModel(
                        $recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\TravelReqNotificationModel::class, $adapter);

        $notification->destination = $request->destination;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->purpose = $request->purpose;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->requestedType = $request->requestedType;

        $notification->status = $status;
        
        switch($request->requestedType){
            case self::TRAVEL_ADVANCE_REQUEST:
                $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
                break;
            case self::TRAVEL_EXPENSE_REQUEST :
                $notification->route = json_encode(["route" => "travelRequest", "action" => "viewExpense", "id" => $request->travelId]);
                break;
            default:
                $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
                break;
        }
        $title = "Travel Recommendation";
        $desc = "Recommendation of Travel Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 10, $adapter, $context);
    }

    private static function travelApprove(TravelRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new TravelRequestRepository($adapter), $request, $request->travelId);
        $recommdAppModel = self::findRecAppForTrvl($request->employeeId, $adapter,$request->approverRole);
        $notification = self::initializeNotificationModel(
                        $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\TravelReqNotificationModel::class, $adapter);

        $notification->destination = $request->destination;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->purpose = $request->purpose;
        $notification->requestedAmount = $request->requestedAmount;
        $notification->requestedType = $request->requestedType;

        $notification->status = $status;

        switch($request->requestedType){
            case self::TRAVEL_ADVANCE_REQUEST:
                $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
                break;
            case self::TRAVEL_EXPENSE_REQUEST :
                $notification->route = json_encode(["route" => "travelRequest", "action" => "viewExpense", "id" => $request->travelId]);
                break;
            default:
                $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
                break;
        }
        $title = "Travel Approval";
        $desc = "Approval of Travel Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 11, $adapter, $context);
    }

    private static function trainingAssigned(TrainingAssign $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        $notification = self::initializeNotificationModel($request->createdBy, $request->employeeId, \Notification\Model\TrainingReqNotificationModel::class, $adapter);

        $training = new Training();
        self::initFullModel(new TrainingRepository($adapter), $training, $request->trainingId);

        $notification->duration = $training->duration;
        $notification->endDate = $training->endDate;
        $notification->startDate = $training->startDate;
        $notification->instructorName = $training->instructorName;
        $notification->trainingCode = $training->trainingCode;
        $notification->trainingName = $training->trainingName;
        $notification->trainingType = $training->trainingType;
        $notification->status = $type;


        $notification->route = json_encode(["route" => "trainingList", "action" => "view", "employeeId" => $request->employeeId, "trainingId" => $request->trainingId]);
        $title = "Training $type";
        $desc = "Training $type";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 12, $adapter, $context);
    }

    private static function loanApplied(LoanRequest $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new LoanRequestRepository($adapter), $request, $request->loanRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $roleAndId = self::findRoleType($recommdAppModel, $request->employeeId);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], \Notification\Model\LoanRequestNotificationModel::class, $adapter);

        $notification->approvedAmount = $request->approvedAmount;
        $notification->deductOnSalary = $request->deductOnSalary;
        $notification->loanDate = $request->loanDate;
        $notification->reason = $request->reason;
        $notification->requestedAmount = $request->requestedAmount;

        $notification->route = json_encode(["route" => "loanApprove", "action" => "view", "id" => $request->loanRequestId, "role" => $roleAndId['role']]);
        $title = "Loan Request";
        $desc = "Loan Request";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 13, $adapter, $context);
    }

    private static function loanRecommend(LoanRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new LoanRequestRepository($adapter), $request, $request->loanRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\LoanRequestNotificationModel::class, $adapter);

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
        self::sendEmail($notification, 14, $adapter, $context);
    }

    private static function loanApprove(LoanRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new LoanRequestRepository($adapter), $request, $request->loanRequestId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\LoanRequestNotificationModel::class, $adapter);

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
        self::sendEmail($notification, 15, $adapter, $context);
    }

    private static function workOnDayOffApplied(WorkOnDayoff $workOnDayoff, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new WorkOnDayoffRepository($adapter), $workOnDayoff, $workOnDayoff->id);

        $recommdAppModel = self::findRecApp($workOnDayoff->employeeId, $adapter);
        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $workOnDayoffReqNotiMod = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], WorkOnDayoffNotificationModel::class, $adapter);

        $workOnDayoffReqNotiMod->route = json_encode(["route" => "dayoffWorkApprove", "action" => "view", "id" => $workOnDayoff->id, "role" => $roleAndId['role']]);
        $workOnDayoffReqNotiMod->fromDate = $workOnDayoff->fromDate;
        $workOnDayoffReqNotiMod->toDate = $workOnDayoff->toDate;
        $workOnDayoffReqNotiMod->duration = $workOnDayoff->duration;
        $workOnDayoffReqNotiMod->remarks = $workOnDayoff->remarks;

        $notificationTitle = "Work On Day-off Request";
        $notificationDesc = "Work On Day-off Request of $workOnDayoffReqNotiMod->fromName from $workOnDayoffReqNotiMod->fromDate to $workOnDayoffReqNotiMod->toDate";

        self::addNotifications($workOnDayoffReqNotiMod, $notificationTitle, $notificationDesc, $adapter);
        self::sendEmail($workOnDayoffReqNotiMod, 16, $adapter, $context);
    }

    private static function workOnDayOffRecommend(WorkOnDayoff $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new WorkOnDayoffRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], WorkOnDayoffNotificationModel::class, $adapter);

        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = $request->duration;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "workOnDayoff", "action" => "view", "id" => $request->id]);
        $title = "Work On Day-off Recommendation";
        $desc = "Recommendation of Work on Day-off Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 17, $adapter, $context);
    }

    private static function workOnDayOffApprove(WorkOnDayoff $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new WorkOnDayoffRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel(
                        $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], WorkOnDayoffNotificationModel::class, $adapter);

        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = $request->duration;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "workOnDayoff", "action" => "view", "id" => $request->id]);
        $title = "Work On Day-off Approval";
        $desc = "Approval of Work on Day-off Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 18, $adapter, $context);
    }

    private static function workOnHoliday(WorkOnHoliday $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new WorkOnHolidayRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], WorkOnHolidayNotificationModel::class, $adapter);

        $holidayName = self::getName($request->holidayId, new HolidayRepository($adapter), 'HOLIDAY_ENAME');

        $notification->route = json_encode(["route" => "holidayWorkApprove", "action" => "view", "id" => $request->id, "role" => $roleAndId['role']]);
        $notification->holidayName = $holidayName;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = $request->duration;
        $notification->remarks = $request->remarks;

        $title = "Work On Holiday Request";
        $desc = "Work On Holiday Request of $notification->fromName from $notification->fromDate to $notification->toDate";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 19, $adapter, $context);
    }

    private static function workOnHolidayRecommend(WorkOnHoliday $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new WorkOnHolidayRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], WorkOnHolidayNotificationModel::class, $adapter);

        $holidayName = self::getName($request->holidayId, new HolidayRepository($adapter), 'HOLIDAY_ENAME');
        $notification->holidayName = $holidayName;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = $request->duration;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "workOnHoliday", "action" => "view", "id" => $request->id]);
        $title = "Work On Holiday Recommendation";
        $desc = "Recommendation of Work on Holiday Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 20, $adapter, $context);
    }

    private static function workOnHolidayApprove(WorkOnHoliday $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new WorkOnHolidayRepository($adapter), $request, $request->id);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel(
                        $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], WorkOnHolidayNotificationModel::class, $adapter);

        $holidayName = self::getName($request->holidayId, new HolidayRepository($adapter), 'HOLIDAY_ENAME');
        $notification->holidayName = $holidayName;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = $request->duration;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "workOnHoliday", "action" => "view", "id" => $request->id]);
        $title = "Work On Holiday Approval";
        $desc = "Approval of Work on Holiday Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 21, $adapter, $context);
    }

    private static function trainingApplied(TrainingRequest $request, AdapterInterface $adapter,AbstractActionController $context, $type) {
        $trainingRequestRepo = new TrainingRequestRepository($adapter);
        $trainingRequestDetail = $trainingRequestRepo->fetchById($request->requestId);
        $request->exchangeArrayFromDB($trainingRequestDetail);

        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);
        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], TrainingReqNotificationModel::class, $adapter);

        $notification->route = json_encode(["route" => "trainingApprove", "action" => "view", "id" => $request->requestId, "role" => $roleAndId['role']]);

        if ($trainingRequestDetail['TRAINING_ID'] != 0) {
            $trainingRequestDetail['START_DATE'] = $trainingRequestDetail['T_START_DATE'];
            $trainingRequestDetail['END_DATE'] = $trainingRequestDetail['T_END_DATE'];
            $trainingRequestDetail['DURATION'] = $trainingRequestDetail['T_DURATION'];
            $trainingRequestDetail['TRAINING_TYPE'] = $trainingRequestDetail['T_TRAINING_TYPE'];
            $trainingRequestDetail['TITLE'] = $trainingRequestDetail['TRAINING_NAME'];
        }
        $getValueComType = function($trainingTypeId) {
            if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            } else if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            }
        };

        $notification->trainingType = $getValueComType($trainingRequestDetail['TRAINING_TYPE']);
        $notification->trainingName = $trainingRequestDetail['TITLE'];
        $notification->trainingCode = $trainingRequestDetail['TRAINING_CODE'];
        $notification->instructorName = $trainingRequestDetail['INSTRUCTOR_NAME'];
        $notification->fromDate = $trainingRequestDetail['START_DATE'];
        $notification->toDate = $trainingRequestDetail['END_DATE'];
        $notification->duration = $trainingRequestDetail['DURATION'];
        $notification->remarks = $request->remarks;

        $title = "Trining Request";
        $desc = "Trining Request of $notification->fromName from $notification->fromDate to $notification->toDate";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 22, $adapter, $context);
    }

    private static function trainingRecommend(TrainingRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        $trainingRequestRepo = new TrainingRequestRepository($adapter);
        $trainingRequestDetail = $trainingRequestRepo->fetchById($request->requestId);
        $request->exchangeArrayFromDB($trainingRequestDetail);

        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], TrainingReqNotificationModel::class, $adapter);

        if ($trainingRequestDetail['TRAINING_ID'] != 0) {
            $trainingRequestDetail['START_DATE'] = $trainingRequestDetail['T_START_DATE'];
            $trainingRequestDetail['END_DATE'] = $trainingRequestDetail['T_END_DATE'];
            $trainingRequestDetail['DURATION'] = $trainingRequestDetail['T_DURATION'];
            $trainingRequestDetail['TRAINING_TYPE'] = $trainingRequestDetail['T_TRAINING_TYPE'];
            $trainingRequestDetail['TITLE'] = $trainingRequestDetail['TRAINING_NAME'];
        }
        $getValueComType = function($trainingTypeId) {
            if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            } else if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            }
        };

        $notification->trainingType = $getValueComType($trainingRequestDetail['TRAINING_TYPE']);
        $notification->trainingName = $trainingRequestDetail['TITLE'];
        $notification->trainingCode = $trainingRequestDetail['TRAINING_CODE'];
        $notification->instructorName = $trainingRequestDetail['INSTRUCTOR_NAME'];
        $notification->fromDate = $trainingRequestDetail['START_DATE'];
        $notification->toDate = $trainingRequestDetail['END_DATE'];
        $notification->duration = $trainingRequestDetail['DURATION'];
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "trainingRequest", "action" => "view", "id" => $request->requestId]);
        $title = "Training Recommendation";
        $desc = "Recommendation of Training Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 23, $adapter, $context);
    }

    private static function trainingApprove(TrainingRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        $trainingRequestRepo = new TrainingRequestRepository($adapter);
        $trainingRequestDetail = $trainingRequestRepo->fetchById($request->requestId);
        $request->exchangeArrayFromDB($trainingRequestDetail);

        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel(
                        $recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], TrainingReqNotificationModel::class, $adapter);

        if ($trainingRequestDetail['TRAINING_ID'] != 0) {
            $trainingRequestDetail['START_DATE'] = $trainingRequestDetail['T_START_DATE'];
            $trainingRequestDetail['END_DATE'] = $trainingRequestDetail['T_END_DATE'];
            $trainingRequestDetail['DURATION'] = $trainingRequestDetail['T_DURATION'];
            $trainingRequestDetail['TRAINING_TYPE'] = $trainingRequestDetail['T_TRAINING_TYPE'];
            $trainingRequestDetail['TITLE'] = $trainingRequestDetail['TRAINING_NAME'];
        }
        $getValueComType = function($trainingTypeId) {
            if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            } else if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            }
        };
        $notification->trainingType = $getValueComType($trainingRequestDetail['TRAINING_TYPE']);
        $notification->trainingName = $trainingRequestDetail['TITLE'];
        $notification->trainingCode = $trainingRequestDetail['TRAINING_CODE'];
        $notification->instructorName = $trainingRequestDetail['INSTRUCTOR_NAME'];
        $notification->fromDate = $trainingRequestDetail['START_DATE'];
        $notification->toDate = $trainingRequestDetail['END_DATE'];
        $notification->duration = $trainingRequestDetail['DURATION'];
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "trainingRequest", "action" => "view", "id" => $request->requestId]);
        $title = "Training Approval";
        $desc = "Approval of Training Request by"
                . " $notification->fromName from $notification->fromDate"
                . " to $notification->toDate is $notification->status";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 24, $adapter, $context);
    }

    private static function leaveSubstituteApplied(LeaveApply $request, AdapterInterface $adapter, AbstractActionController $context) {
        self::initFullModel(new LeaveApplyRepository($adapter), $request, $request->id);

        $leaveSubstituteRepo = new LeaveSubstituteRepository($adapter);
        $leaveSubstituteDetail = $leaveSubstituteRepo->fetchById($request->id);

        $notification = self::initializeNotificationModel($request->employeeId, $leaveSubstituteDetail['EMPLOYEE_ID'], LeaveSubNotificationModel::class, $adapter);

        $leaveName = self::getName($request->leaveId, new LeaveMasterRepository($adapter), 'LEAVE_ENAME');
        $notification->leaveName = $leaveName;
        $notification->fromDate = $request->startDate;
        $notification->toDate = $request->endDate;
        $notification->duration = $request->noOfDays;
        $notification->remarks = $request->remarks;

        $notification->route = json_encode(["route" => "leaveNotification", "action" => "view", "id" => $request->id]);
        $title = "Substitue Work Request On Leave";
        $desc = "Substitue Work Request On Leave From " . $notification->fromDate . " To " . $notification->toDate;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 25, $adapter, $context);
    }

    private static function leaveSubstituteAccepted(LeaveApply $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new LeaveApplyRepository($adapter), $request, $request->id);
        $leaveSubstituteRepo = new LeaveSubstituteRepository($adapter);
        $leaveSubstituteDetail = $leaveSubstituteRepo->fetchById($request->id);

        $notification = self::initializeNotificationModel($leaveSubstituteDetail['EMPLOYEE_ID'], $request->employeeId, LeaveSubNotificationModel::class, $adapter);

        $leaveName = self::getName($request->leaveId, new LeaveMasterRepository($adapter), 'LEAVE_ENAME');
        $notification->leaveName = $leaveName;
        $notification->fromDate = $request->startDate;
        $notification->toDate = $request->endDate;
        $notification->duration = $request->noOfDays;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "leaverequest", "action" => "view", "id" => $request->id]);
        $title = "Substitue Work On Leave Recommendation";
        $desc = "Substitue Work Request On Leave From " . $notification->fromDate . " To " . $notification->toDate . " is " . $status;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 26, $adapter, $context);
    }

    private static function travelSubstituteApplied(TravelRequest $request, AdapterInterface $adapter, AbstractActionController $context) {
        self::initFullModel(new TravelRequestRepository($adapter), $request, $request->travelId);

        $travelSubstituteRepo = new TravelSubstituteRepository($adapter);
        $travelSubstituteDetail = $travelSubstituteRepo->fetchById($request->travelId);

        $notification = self::initializeNotificationModel($request->employeeId, $travelSubstituteDetail['EMPLOYEE_ID'], TravelSubNotificationModel::class, $adapter);

        $notification->travelCode = $request->travelCode;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = ($request->toDate - $request->fromDate) + 1;
        $notification->destination = $request->destination;
        $notification->purpose = $request->purpose;
        $notification->remarks = $request->remarks;

        $notification->route = json_encode(["route" => "travelNotification", "action" => "view", "id" => $request->travelId]);
        $title = "Substitue Work Request On Travel";
        $desc = "Substitue Work Request On Travel From " . $notification->fromDate . " To " . $notification->toDate;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 27, $adapter, $context);
    }

    private static function travelSubstituteAccepted(TravelRequest $request, AdapterInterface $adapter, AbstractActionController $context, string $status) {
        self::initFullModel(new TravelRequestRepository($adapter), $request, $request->travelId);

        $travelSubstituteRepo = new TravelSubstituteRepository($adapter);
        $travelSubstituteDetail = $travelSubstituteRepo->fetchById($request->travelId);

        $notification = self::initializeNotificationModel($travelSubstituteDetail['EMPLOYEE_ID'], $request->employeeId, TravelSubNotificationModel::class, $adapter);

        $notification->travelCode = $request->travelCode;
        $notification->fromDate = $request->fromDate;
        $notification->toDate = $request->toDate;
        $notification->duration = ($request->toDate - $request->fromDate) + 1;
        $notification->destination = $request->destination;
        $notification->purpose = $request->purpose;
        $notification->remarks = $request->remarks;
        $notification->status = $status;

        $notification->route = json_encode(["route" => "travelRequest", "action" => "view", "id" => $request->travelId]);
        $title = "Substitue Work On Travel Recommendation";
        $desc = "Substitue Work Request On Travel From " . $notification->fromDate . " To " . $notification->toDate . " is " . $status;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 28, $adapter, $context);
    }

    private static function forgotPassword(ForgotPassword $forgotPassword, AdapterInterface $adapter) {
        $isValidEmail = function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        };

        $employeeRepo = new EmployeeRepository($adapter);
        $toEmployee = $employeeRepo->fetchById($forgotPassword->employeeId);
        $toEmail = $toEmployee['EMAIL_OFFICIAL'];
        $toName = $toEmployee['FIRST_NAME'] . " " . $toEmployee['MIDDLE_NAME'] . " " . $toEmployee['LAST_NAME'];

        try {
            $mail = new Message();
            $mail->setSubject($forgotPassword->code . " is your password recovery code");
            $htmlDescription = "Hi " . $toName . ", You can enter the following reset code<br>" . $forgotPassword->code . "<br><br>Your Code will be expired in " . $forgotPassword->expiryDate;
            $html2txt = new Html2Text($htmlDescription);
            $mail->setBody($html2txt->getText());

            if (!isset($toEmail) || $toEmail == null || $toEmail == '' || !$isValidEmail($toEmail)) {
                throw new Exception("Receiver email is not set or valid.");
            }
            $mail->addTo($toEmail, $toName);

            EmailHelper::sendEmail($mail);
        } catch (Exception $e) {
            print "<pre>";
            print($e->getMessage());
            exit;
        }
    }

    private static function salaryReview(SalaryDetail $request, AdapterInterface $adapter, AbstractActionController $context) {
        self::initFullModel(new SalaryDetailRepo($adapter), $request, $request->salaryDetailId);
        $notification = self::initializeNotificationModel($request->createdBy, $request->employeeId, SalaryReviewNotificationModel::class, $adapter);

        $notification->newAmount = $request->newAmount;
        $notification->oldAmount = $request->oldAmount;
        $notification->effectiveDate = $request->effectiveDate;

        $notification->route = json_encode(["route" => "salaryReview", "action" => "edit", "id" => $request->salaryDetailId]);
        $title = "Salary Review";
        $desc = "Salary Review From " . $notification->oldAmount . " To " . $notification->newAmount;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 29, $adapter, $context);
    }

    private static function kpiSetting(AppraisalStatus $request, AdapterInterface $adapter, AbstractActionController $context, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($request->employeeId, $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        }

        $title = "KPI Setting on Appraisal";
        $desc = "KPI Set by"
                . " $notification->fromName on $notification->appraisalName of type $notification->appraisalType";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 30, $adapter, $context);
    }

    private static function kpiApproved(AppraisalStatus $request, AdapterInterface $adapter, AbstractActionController $context, $senderDetail, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($senderDetail['ID'], $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
//                print_r($recieverDetail['USER_TYPE']);
//                print_r("hellow"); die();
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        } else if ($recieverDetail['USER_TYPE'] == 'APPRAISEE') {
//                print_r($recieverDetail['USER_TYPE']);
//                print_r("hellow"); die();
            $notification->route = json_encode(["route" => "performanceAppraisal", "action" => "view", "appraisalId" => $request->appraisalId]);
        }

        $title = "KPI Approval";
        $desc = "KPI Approved by"
                . " $notification->fromName on $notification->appraisalName of type $notification->appraisalType";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 31, $adapter, $context);
    }

    private static function keyAchievement(AppraisalStatus $request, AdapterInterface $adapter, AbstractActionController $context, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($request->employeeId, $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        }

        $title = "Key Achievement Update on Appraisal";
        $desc = "Key Achievement Updated by"
                . " $notification->fromName on $notification->appraisalName of type $notification->appraisalType";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 32, $adapter, $context);
    }

    private static function appraisalEvaluation(AppraisalStatus $request, AdapterInterface $adapter, AbstractActionController $context, $senderDetail, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($senderDetail['ID'], $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        } else if ($recieverDetail['USER_TYPE'] == 'APPRAISEE') {
            $notification->route = json_encode(["route" => "performanceAppraisal", "action" => "view", "appraisalId" => $request->appraisalId]);
        }

        $title = "Appraisal Evaluation";
        $desc = "Appraisal Evaluated by"
                . " $notification->fromName of type $notification->appraisalType";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 33, $adapter, $context);
    }

    private static function appraisalReview(AppraisalStatus $request, AdapterInterface $adapter, AbstractActionController $context, $type, $senderDetail, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($senderDetail['ID'], $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        } else if ($recieverDetail['USER_TYPE'] == 'APPRAISEE') {
            $notification->route = json_encode(["route" => "performanceAppraisal", "action" => "view", "appraisalId" => $request->appraisalId]);
        } else if ($recieverDetail['USER_TYPE'] == 'SUPER_REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-final-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        }
        $getValue = function($val) {
            if ($val != null && $val != "") {
                if ($val == 'Y')
                    return "Agreed";
                else if ($val == 'N')
                    return "Disgreed";
            }else {
                return "";
            }
        };
        $agree = ($type == 'REVIEWER_EVALUATION') ? $assignedAppraisalDetail['REVIEWER_AGREE'] : $assignedAppraisalDetail['SUPER_REVIEWER_AGREE'];
        $title = "Appraisal Review";
        if ($agree == null) {
            $desc = "Appraisal reviewed";
        } else {
            $desc = $getValue($agree);
        }
        $desc .= " by " . $notification->fromName . " on " . $notification->appraisalName . " of type " . $notification->appraisalType;

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 34, $adapter, $context);
    }

    private static function appraiseeFeedback(AppraisalStatus $request, AdapterInterface $adapter,AbstractActionController $context, $recieverDetail) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($request->employeeId, $recieverDetail['ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        if ($recieverDetail['USER_TYPE'] == 'APPRAISER') {
            $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);
        } else if ($recieverDetail['USER_TYPE'] == 'HR') {
            $notification->route = json_encode(["route" => "appraisalReport", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        } else if ($recieverDetail['USER_TYPE'] == 'SUPER_REVIEWER') {
            $notification->route = json_encode(["route" => "appraisal-final-review", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId]);
        }

        $getValue = function($val) {
            if ($val != null && $val != "") {
                if ($val == 'Y')
                    return "Agreed";
                else if ($val == 'N')
                    return "Disagreed";
            }else {
                return "";
            }
        };
        $title = "Final Feedback on Appraisal";
        $desc = ($assignedAppraisalDetail['APPRAISEE_AGREE'] == null) ? "Feedback" : $getValue($assignedAppraisalDetail['APPRAISEE_AGREE']);
        $desc .= " by $notification->fromName on $notification->appraisalName of type $notification->appraisalType";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 35, $adapter, $context);
    }

    public static function monthlyAppraisalAssigned(AppraisalAssign $request, AdapterInterface $adapter, AbstractActionController $context) {
        $appraisalAssignRepo = new AppraisalAssignRepository($adapter);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($request->employeeId, $request->appraisalId);

        $fullName = function($id, $adapter) {
            if ($id != null) {
                $empRepository = new EmployeeRepository($adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            } else {
                return "";
            }
        };
        $notification = self::initializeNotificationModel($request->createdBy, $assignedAppraisalDetail['APPRAISER_ID'], AppraisalNotificationModel::class, $adapter);

        $notification->appraisalName = $assignedAppraisalDetail['APPRAISAL_EDESC'];
        $notification->appraisalType = $assignedAppraisalDetail['APPRAISAL_TYPE_EDESC'];
        $notification->appraiseeName = $fullName($assignedAppraisalDetail['EMPLOYEE_ID'], $adapter);
        $notification->appraiserName = $fullName($assignedAppraisalDetail['APPRAISER_ID'], $adapter);
        $notification->reviewerName = $fullName($assignedAppraisalDetail['REVIEWER_ID'], $adapter);
        $notification->startDate = $assignedAppraisalDetail['START_DATE'];
        $notification->endDate = $assignedAppraisalDetail['END_DATE'];
        $notification->rating = $assignedAppraisalDetail['APPRAISER_OVERALL_RATING'];
        $notification->currentStage = $assignedAppraisalDetail['STAGE_EDESC'];

        $notification->route = json_encode(["route" => "appraisal-evaluation", "action" => "view", "appraisalId" => $request->appraisalId, "employeeId" => $request->employeeId, "tab" => 1]);

        $title = "Monthly Appraisal Assigned";
        $desc = "$notification->appraisalName for $notification->appraiseeName is ready to evaluate";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 40, $adapter, $context);
    }

    private static function overtimeApplied(Overtime $request, AdapterInterface $adapter, AbstractActionController $context, $type) {
        self::initFullModel(new OvertimeRepository($adapter), $request, $request->overtimeId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $roleAndId = self::findRoleType($recommdAppModel, $type);
        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::EMPLOYEE_ID], $roleAndId['id'], \Notification\Model\OvertimeReqNotificationModel::class, $adapter);

        $keys = get_object_vars($notification);
        foreach ($keys as $v) {
            if (!isset($notification->{$v}) && isset($request->{$v})) {
                $notification->{$v} = $request->{$v};
            }
        }

        $notification->route = json_encode(["route" => "overtimeApprove", "action" => "view", "id" => $request->overtimeId, "role" => $roleAndId['role']]);

        $title = "Overtime Request";
        $desc = "Overtime Request Applied";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 37, $adapter, $context);
    }

    private static function overtimeRecommend(Overtime $request, AdapterInterface $adapter, AbstractActionController $context, $status) {
        self::initFullModel(new OvertimeRepository($adapter), $request, $request->overtimeId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::RECOMMEND_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\OvertimeReqNotificationModel::class, $adapter);

        $keys = get_object_vars($notification);
        foreach ($keys as $v) {
            if (!isset($notification->{$v}) && isset($request->{$v})) {
                $notification->{$v} = $request->{$v};
            }
        }
        $notification->status = $status;

        $notification->route = json_encode(["route" => "overtimeRequest", "action" => "view", "id" => $request->overtimeId]);

        $title = "Overtime Request";
        $desc = "Recommendation of Overtime request is {$status}";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 38, $adapter, $context);
    }

    private static function overtimeApprove(Overtime $request, AdapterInterface $adapter,AbstractActionController $context, $status) {
        self::initFullModel(new OvertimeRepository($adapter), $request, $request->overtimeId);
        $recommdAppModel = self::findRecApp($request->employeeId, $adapter);

        $notification = self::initializeNotificationModel($recommdAppModel[RecommendApprove::APPROVED_BY], $recommdAppModel[RecommendApprove::EMPLOYEE_ID], \Notification\Model\OvertimeReqNotificationModel::class, $adapter);

        $keys = get_object_vars($notification);
        foreach ($keys as $v) {
            if (!isset($notification->{$v}) && isset($request->{$v})) {
                $notification->{$v} = $request->{$v};
            }
        }
        $notification->status = $status;

        $notification->route = json_encode(["route" => "overtimeRequest", "action" => "view", "id" => $request->overtimeId]);

        $title = "Overtime Request";
        $desc = "Approval of Overtime request is {$status}";

        self::addNotifications($notification, $title, $desc, $adapter);
        self::sendEmail($notification, 39, $adapter, $context);
    }

    public static function pushNotification(int $eventType, Model $model, AdapterInterface $adapter, AbstractActionController $context = null, $senderDetail = null, $receiverDetail = null) {
        $url = null;
        if ($context != null) {
            $url = $context->plugin('url');
        }
        switch ($eventType){
            case NotificationEvents::LEAVE_APPLIED:
                self::leaveApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_ACCEPTED:
                self::leaveRecommend($model, $adapter, $context, self::ACCEPTED);
                self::leaveApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::LEAVE_RECOMMEND_REJECTED:
                self::leaveRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::LEAVE_APPROVE_ACCEPTED:
                self::leaveApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::LEAVE_APPROVE_REJECTED:
                self::leaveApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::ATTENDANCE_APPLIED:
                self::attendanceRequest($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::ATTENDANCE_RECOMMEND_ACCEPTED:
                self::attendanceRecommend($model, $adapter, $context, self::ACCEPTED);
                self::attendanceRequest($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::ATTENDANCE_RECOMMEND_REJECTED:
                self::attendanceRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED:
                self::attendanceApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::ATTENDANCE_APPROVE_REJECTED:
                self::attendanceApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::ADVANCE_APPLIED:
                self::advanceApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED:
                self::advanceRecommend($model, $adapter, $context, self::ACCEPTED);
                self::advanceApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::ADVANCE_RECOMMEND_REJECTED:
                self::advanceRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::ADVANCE_APPROVE_ACCEPTED:
                self::advanceApprove($model, $adapter, $context);
                break;
            case NotificationEvents::ADVANCE_APPROVE_REJECTED:
                self::advanceApprove($model, $adapter, $context);
                break;
            case NotificationEvents::ADVANCE_CANCELLED:
//                ${"fn" . NotificationEvents::ADVANCE_CANCELLED}($model, $adapter, $context);
                break;
            case NotificationEvents::TRAVEL_APPLIED:
                self::travelApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED:
                self::travelRecommend($model, $adapter, $context, self::ACCEPTED);
                self::travelApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::TRAVEL_RECOMMEND_REJECTED:
                self::travelRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::TRAVEL_APPROVE_ACCEPTED:
                self::travelApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::TRAVEL_APPROVE_REJECTED:
                self::travelApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::TRAVEL_CANCELLED:
//                ${"fn" . NotificationEvents::TRAVEL_CANCELLED}($model, $adapter, $context);
                break;
            case NotificationEvents::TRAINING_ASSIGNED:
                self::trainingAssigned($model, $adapter, $context, self::ASSIGNED);
                break;
            case NotificationEvents::TRAINING_CANCELLED:
                self::trainingAssigned($model, $adapter, $context, self::CANCELLED);
                break;
            case NotificationEvents::LOAN_APPLIED:
                self::loanApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::LOAN_RECOMMEND_ACCEPTED:
                self::loanRecommend($model, $adapter, $context, self::ACCEPTED);
                self::loanApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::LOAN_RECOMMEND_REJECTED:
                self::loanRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::LOAN_APPROVE_ACCEPTED:
                self::loanApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::LOAN_APPROVE_REJECTED:
                self::loanApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPLIED:
                self::workOnDayOffApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED:
                self::workOnDayOffRecommend($model, $adapter, $context, self::ACCEPTED);
                self::workOnDayOffApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::WORKONDAYOFF_RECOMMEND_REJECTED:
                self::workOnDayOffRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED:
                self::workOnDayOffApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::WORKONDAYOFF_APPROVE_REJECTED:
                self::workOnDayOffApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPLIED:
                self::workOnHoliday($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED:
                self::workOnHolidayRecommend($model, $adapter, $context, self::ACCEPTED);
                self::workOnHoliday($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::WORKONHOLIDAY_RECOMMEND_REJECTED:
                self::workOnHolidayRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED:
                self::workOnHolidayApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED:
                self::workOnHolidayApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::TRAINING_APPLIED:
                self::trainingApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::TRAINING_RECOMMEND_ACCEPTED:
                self::trainingRecommend($model, $adapter, $context, self::ACCEPTED);
                self::trainingApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::TRAINING_RECOMMEND_REJECTED:
                self::trainingRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::TRAINING_APPROVE_ACCEPTED:
                self::trainingApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::TRAINING_APPROVE_REJECTED:
                self::trainingApprove($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::LEAVE_SUBSTITUTE_APPLIED:
                self::leaveSubstituteApplied($model, $adapter, $context);
                break;
            case NotificationEvents::LEAVE_SUBSTITUTE_ACCEPTED:
                self::leaveSubstituteAccepted($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::LEAVE_SUBSTITUTE_REJECTED:
                self::leaveSubstituteAccepted($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::TRAVEL_SUBSTITUTE_APPLIED:
                self::travelSubstituteApplied($model, $adapter, $context);
                break;
            case NotificationEvents::TRAVEL_SUBSTITUTE_ACCEPTED:
                self::travelSubstituteAccepted($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::TRAVEL_SUBSTITUTE_REJECTED:
                self::travelSubstituteAccepted($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::FORGOT_PASSWORD:
                self::forgotPassword($model, $adapter, $senderDetail);
                break;
            case NotificationEvents::SALARY_REVIEW:
                self::salaryReview($model, $adapter, $context);
                break;
            case NotificationEvents::KPI_SETTING:
                self::kpiSetting($model, $adapter, $context, $receiverDetail);
                break;
            case NotificationEvents::KPI_APPROVED:
                self::kpiApproved($model, $adapter, $context, $senderDetail, $receiverDetail);
                break;
            case NotificationEvents::KEY_ACHIEVEMENT:
                self::keyAchievement($model, $adapter, $context, $receiverDetail);
                break;
            case NotificationEvents::APPRAISAL_EVALUATION:
                self::appraisalEvaluation($model, $adapter, $context, $senderDetail, $receiverDetail);
                break;
            case NotificationEvents::APPRAISAL_REVIEW:
                self::appraisalReview($model, $adapter, $context, self::REVIEWER_EVALUATION, $senderDetail, $receiverDetail);
                break;
            case NotificationEvents::APPRAISAL_FINAL_REVIEW:
                self::appraisalReview($model, $adapter, $context, self::SUPER_REVIEWER_EVALUATION, $senderDetail, $receiverDetail);
                break;
            case NotificationEvents::HR_FEEDBACK:
                self::appraisalReview($model, $adapter, $context, self::HR_FEEDBACK, $senderDetail, $receiverDetail);
                break;
            case NotificationEvents::APPRAISEE_FEEDBACK:
                self::appraiseeFeedback($model, $adapter, $context, $receiverDetail);
                break;
            case NotificationEvents::MONTHLY_APPRAISAL_ASSIGNED:
                self::monthlyAppraisalAssigned($model, $adapter, $context);
                break;
            case NotificationEvents::OVERTIME_APPLIED:
                self::overtimeApplied($model, $adapter, $context, self::RECOMMENDER);
                break;
            case NotificationEvents::OVERTIME_RECOMMEND_ACCEPTED:
                self::overtimeRecommend($model, $adapter, $context, self::ACCEPTED);
                self::overtimeApplied($model, $adapter, $context, self::APPROVER);
                break;
            case NotificationEvents::OVERTIME_RECOMMEND_REJECTED:
                self::overtimeRecommend($model, $adapter, $context, self::REJECTED);
                break;
            case NotificationEvents::OVERTIME_APPROVE_ACCEPTED:
                self::overtimeApprove($model, $adapter, $context, self::ACCEPTED);
                break;
            case NotificationEvents::OVERTIME_APPROVE_REJECTED:
                self::overtimeApprove($model, $adapter, $context, self::REJECTED);
                break;
        }
    }
    public static function mailHeader(AbstractActionController $context){
        $basePath = $context->getRequest()->getBasePath();
        $headerImg =  "<div style='background-color:#F48B2F; width:100%;text-align:center;'><img src='http://laxmi.laxmibank.com/assets/upload/images/config/logo2.gif' align='middle' style='text-align:center'></div>";
//        echo $headerImg; die();
        return $headerImg;
    }
    public static function mailFooter(AbstractActionController $context){
        $footer = "<div style='background-color:#F48B2F;font-size:11px; height:80px; text-align:center;padding:10px;'>
       <label style='margin-bottom:20px'>Disclaimer: This is an automatically generated email. </label><br/>
<label style='margin-bottom:20px'>\r\nConfidentiality Clause: This electronic mail is confidential, privileged and only for the use of the recipient to whom it is addressed.</label><br/>
<label style='margin-bottom:20px'>\r\nIf you are not the intended recipient, you are hereby notified that any retention, dissemination, distribution or copying of this message is strictly prohibited. If you 
have received this message in error please notify us immediately at hr@laxmibank.com and delete the message immediately. Thank you.</label><br/><br/></div>";
        return $footer; 
    }
    private static function initializeNotificationModel($fromId, $toId, $class, AdapterInterface $adapter) {
        $employeeRepo = new EmployeeRepository($adapter);
        $fromEmployee = $employeeRepo->fetchById($fromId);
        $toEmployee = $employeeRepo->fetchById($toId);

        $notification = new $class();

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

        return $notification;
    }

    private static function findRoleType($recAppModel, $type) {
        $id = '';
        $role = '';
        switch ($type) {
            case self::RECOMMENDER:
                $id = $recAppModel[RecommendApprove::RECOMMEND_BY];
                $role = RecommendApprove::RECOMMENDER_VALUE;
                break;
            case self::APPROVER:
                $id = $recAppModel[RecommendApprove::APPROVED_BY];
                $role = RecommendApprove::APPROVER_VALUE;
                break;
        }
        if ($recAppModel[RecommendApprove::RECOMMEND_BY] == $recAppModel[RecommendApprove::APPROVED_BY]) {
            $id = $recAppModel[RecommendApprove::RECOMMEND_BY];
            $role = RecommendApprove::BOTH_VALUE;
        }

        return ['id' => $id, 'role' => $role];
    }

    private static function findRecApp($employeeId, $adapter) {
        $recommdAppRepo = new RecommendApproveRepository($adapter);
        $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($employeeId);

        if ($recommdAppModel == null) {
            throw new Exception("recommender and approver not set for employee with id =>" . $employeeId);
        }

        return $recommdAppModel;
    }
    
    public static function findRecAppForTrvl($employeeId,$adapter,$approverRole){
        $recommdAppRepo = new RecommendApproveRepository($adapter);
        $empRepository = new EmployeeRepository($adapter);
        
        $recommdAppModel = $recommdAppRepo->getDetailByEmployeeID($employeeId);
        $approverFlag =($approverRole=='DCEO')? [HrEmployees::IS_DCEO=>'Y']:[HrEmployees::IS_CEO=>'Y'];
        $whereCondition = array_merge([HrEmployees::STATUS=>'E', HrEmployees::RETIRED_FLAG=>'N'],$approverFlag);
        $approverDetail = $empRepository->fetchByCondition($whereCondition);
        
        $recommdAppModel[RecommendApprove::RECOMMEND_BY]=$recommdAppModel[RecommendApprove::APPROVED_BY];  
        $recommdAppModel[RecommendApprove::APPROVED_BY] = $approverDetail['EMPLOYEE_ID'];
        
        if ($recommdAppModel == null) {
            throw new Exception("recommender and approver not set for employee with id =>" . $employeeId);
        }

        return $recommdAppModel;
    }
}
