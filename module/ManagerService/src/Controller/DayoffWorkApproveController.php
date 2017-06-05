<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use DateTime;
use Exception;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Repository\LeaveAssignRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Repository\DayoffWorkApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\WorkOnDayoffForm;
use SelfService\Model\WorkOnDayoff;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class DayoffWorkApproveController extends AbstractActionController {

    private $dayoffWorkApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->dayoffWorkApproveRepository = new DayoffWorkApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnDayoffForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
//        print_r($this->employeeId); die();
        $list = $this->dayoffWorkApproveRepository->getAllRequest($this->employeeId);

        $dayoffWorkRequest = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getStatusValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
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
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            $dataArray = [
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'FROM_DATE' => $row['FROM_DATE'],
                'TO_DATE' => $row['TO_DATE'],
                'DURATION' => $row['DURATION'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REMARKS' => $row['REMARKS'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'ID' => $row['ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($dayoffWorkRequest, $dataArray);
        }
        return Helper::addFlashMessagesToArray($this, ['dayoffWorkRequest' => $dayoffWorkRequest, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("dayoffWorkApprove");
        }
        $workOnDayoffModel = new WorkOnDayoff();
        $request = $this->getRequest();

        $detail = $this->dayoffWorkApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

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
            $workOnDayoffModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnDayoffModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $workOnDayoffModel->recommendedDate = Helper::getcurrentExpressionDate();
                $workOnDayoffModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnDayoffModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Day-off Request Rejected!!!");
                } else if ($action == "Approve") {
                    $workOnDayoffModel->status = "RC";
                    $this->flashmessenger()->addMessage("Work on Day-off Request Approved!!!");
                }
                $workOnDayoffModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);
                $workOnDayoffModel->id = $id;
                try {
                    HeadNotification::pushNotification(($workOnDayoffModel->status == 'RC') ? NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED : NotificationEvents::WORKONDAYOFF_RECOMMEND_REJECTED, $workOnDayoffModel, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $workOnDayoffModel->approvedDate = Helper::getcurrentExpressionDate();
                $workOnDayoffModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnDayoffModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Day-off Request Rejected!!!");
                } else if ($action == "Approve") {
                    $leaveMasterRepo = new LeaveMasterRepository($this->adapter);
                    $leaveAssignRepo = new LeaveAssignRepository($this->adapter);
                    $substituteLeave = $leaveMasterRepo->getSubstituteLeave()->getArrayCopy();
                    $substituteLeaveId = $substituteLeave['LEAVE_ID'];
                    $empSubLeaveDtl = $leaveAssignRepo->filterByLeaveEmployeeId($substituteLeaveId, $requestedEmployeeID);
                    if (count($empSubLeaveDtl) > 0) {
                        $preBalance = $empSubLeaveDtl['BALANCE'];
                        $total = $empSubLeaveDtl['TOTAL_DAYS'] + $detail['DURATION'];
                        $balance = $preBalance + $detail['DURATION'];
                        $leaveAssignRepo->updatePreYrBalance($requestedEmployeeID, $substituteLeaveId, 0, $total, $balance);
                    } else {
                        $leaveAssign = new LeaveAssign();
                        $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                        $leaveAssign->createdBy = $this->employeeId;
                        $leaveAssign->employeeId = $requestedEmployeeID;
                        $leaveAssign->leaveId = $substituteLeaveId;
                        $leaveAssign->totalDays = $detail['DURATION'];
                        $leaveAssign->previousYearBalance = 0;
                        $leaveAssign->balance = $detail['DURATION'];
                        $leaveAssignRepo->add($leaveAssign);
                    }
                    $workOnDayoffModel->status = "AP";
                    $this->flashmessenger()->addMessage("Work on Day-off Request Approved");
                }
                if ($role == 4) {
                    $workOnDayoffModel->recommendedBy = $this->employeeId;
                    $workOnDayoffModel->recommendedDate = Helper::getcurrentExpressionDate();
                }

                // to update back date changes
//                $sDate = $detail['FROM_DATE'];
//                $eDate = $detail['TO_DATE'];
//                $currDate = Helper::getCurrentDate();
//                $begin = new DateTime($sDate);
//                $end = new DateTime($eDate);
//                $attendanceDetailModel = new AttendanceDetail();
//                $attendanceDetailModel->dayoffFlag = 'N';
//                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);


//                start of transaction
//                $connection = $this->adapter->getDriver()->getConnection();
//                $connection->beginTransaction();
//                try {
//                    if (strtotime($sDate) <= strtotime($currDate)) {
//                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
//                            $dayOffDate = $i->format("d-M-Y");
//                            if (strtotime($dayOffDate) <= strtotime($currDate)) {
//                                $where = ["EMPLOYEE_ID" => $requestedEmployeeID, "ATTENDANCE_DT" => $dayOffDate];
//                                $attendanceDetailRepo->editWith($attendanceDetailModel, $where);
//                            }
//                        }
//                    }
                    $workOnDayoffModel->approvedRemarks = $getData->approvedRemarks;
                    $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);
                    $workOnDayoffModel->id = $id;
//                    $connection->commit();
//                } catch (exception $e) {
//                    $connection->rollback();
//                    echo "error message:" . $e->getMessage();
//                }
//                end of transaction

//                die();


                try {
                    HeadNotification::pushNotification(($workOnDayoffModel->status == 'AP') ? NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED : NotificationEvents::WORKONDAYOFF_APPROVE_REJECTED, $workOnDayoffModel, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("dayoffWorkApprove");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'status' => $status,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'employeeId' => $this->employeeId,
                    'requestedEmployeeId' => $requestedEmployeeID,
        ]);
    }

    public function statusAction() {
        $status = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

}
