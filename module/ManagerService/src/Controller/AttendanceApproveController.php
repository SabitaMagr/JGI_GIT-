<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Exception;
use ManagerService\Repository\AttendanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AttendanceApproveController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceApproveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm() {
        $attendanceRequestForm = new AttendanceRequestForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceRequestForm);
    }

    public function indexAction() {
        $list = $this->repository->getAllRequest($this->employeeId);
        $attendanceApprove = [];

        $getValue = function($recommender, $approver) {
            if ($recommender = $approver) {
                return 'RECOMMENDER/APPROVER';
            } else {
                if ($this->employeeId == $recommender) {
                    return 'RECOMMENDER';
                } else if ($this->employeeId == $approver) {
                    return 'APPROVER';
                }
            }
        };

        $getStatusValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } elseif ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        $getRole = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 2;
            } else if ($this->employeeId == $approver) {
                return 3;
            }
        };

        foreach ($list as $row) {

            $new_row = array_merge($row, [
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER']),
                'STATUS' => $getStatusValue($row['STATUS'])
            ]);


            array_push($attendanceApprove, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['attendanceApprove' => $attendanceApprove]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');


        if ($id === 0) {
            return $this->redirect()->toRoute("attedanceapprove");
        }
        $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $attendanceRequestRepository->fetchById($id);

        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $fullName($employeeId);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];


        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $RECM_MN = ($detail['RECM_MN'] != null) ? " " . $detail['RECM_MN'] . " " : " ";
        $recommender = $detail['RECM_FN'] . $RECM_MN . $detail['RECM_LN'];
        $APRV_MN = ($detail['APRV_MN'] != null) ? " " . $detail['APRV_MN'] . " " : " ";
        $approver = $detail['APRV_FN'] . $APRV_MN . $detail['APRV_LN'];
        $MN1 = ($detail['MN1'] != null) ? " " . $detail['MN1'] . " " : " ";
        $recommended_by = $detail['FN1'] . $MN1 . $detail['LN1'];
        $MN2 = ($detail['MN2'] != null) ? " " . $detail['MN2'] . " " : " ";
        $approved_by = $detail['FN2'] . $MN2 . $detail['LN2'];
        $authRecommender = ($status == 'RQ') ? $recommender : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || ($status == 'R' && $approvedDT == null)) ? $approver : $approved_by;

        $recommenderId = ($status == 'RQ') ? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];

        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($detail);
            $this->form->bind($model);
        } else {
            $getData = $request->getPost();

            $action = $getData->submit;
            if ($role == 2) {
                $model->recommendedDate = Helper::getcurrentExpressionDate();
                $model->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $model->status = "R";
                    $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $model->status = "RC";
                    $this->flashmessenger()->addMessage("Attendance Request Approved!!!");
                }
                $model->recommendedRemarks = $getData->recommendedRemarks;
                $this->repository->edit($model, $id);

                try {
                    $model->id = $id;
                    HeadNotification::pushNotification(($model->status == 'RC') ? NotificationEvents::ATTENDANCE_RECOMMEND_ACCEPTED : NotificationEvents::ATTENDANCE_RECOMMEND_REJECTED, $model, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $model->approvedDt = Helper::getcurrentExpressionDate();
                $model->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $model->status = "R";
                    $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $model->status = "AP";
                    $this->flashmessenger()->addMessage("Attendance Request Approved");
                }
                if ($role == 4) {
                    $model->recommendedDate = Helper::getcurrentExpressionDate();
                    $model->recommendedBy = $this->employeeId;
                }
                $model->approvedRemarks = $getData->approvedRemarks;
                $this->repository->edit($model, $id);
                $this->repository->backdateAttendance(Helper::getExpressionDate($detail['ATTENDANCE_DT']), $detail['EMPLOYEE_ID'], Helper::getExpressionTime($detail['IN_TIME']), Helper::getExpressionTime($detail['OUT_TIME']));

                try {
                    $model->id = $id;
                    HeadNotification::pushNotification(($model->status == 'AP') ? NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED : NotificationEvents::ATTENDANCE_APPROVE_REJECTED, $model, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("attedanceapprove");
        }


        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'status' => $detail['STATUS'],
                    'employeeName' => $employeeName,
                    'employeeId' => $employeeId,
                    'approver' => $authApprover,
                    'requestedDt' => $detail['REQUESTED_DT'],
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'requestedEmployeeId' => $requestedEmployeeID,
        ]);
    }

    public function statusAction() {
        $attendanceStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $attendanceStatusFormElement = new Select();
        $attendanceStatusFormElement->setName("attendanceStatus");
        $attendanceStatusFormElement->setValueOptions($attendanceStatus);
        $attendanceStatusFormElement->setAttributes(["id" => "attendanceRequestStatusId", "class" => "form-control"]);
        $attendanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'attendanceStatus' => $attendanceStatusFormElement,
                    'approverId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

}
