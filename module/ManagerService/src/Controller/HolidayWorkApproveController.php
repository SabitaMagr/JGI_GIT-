<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Model\Holiday;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Repository\LeaveAssignRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Repository\HolidayWorkApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\HolidayRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class HolidayWorkApproveController extends AbstractActionController {

    private $holidayWorkApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->holidayWorkApproveRepository = new HolidayWorkApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnHolidayForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $list = $this->holidayWorkApproveRepository->getAllRequest($this->employeeId);

        $holidayWorkApprove = [];
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
                'HOLIDAY_ENAME' => $row['HOLIDAY_ENAME'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'ID' => $row['ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($holidayWorkApprove, $dataArray);
        }
        return Helper::addFlashMessagesToArray($this, ['holidayWorkApprove' => $holidayWorkApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        $workOnHolidayModel = new WorkOnHoliday();
        $request = $this->getRequest();

        $detail = $this->holidayWorkApproveRepository->fetchById($id);
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
            $workOnHolidayModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnHolidayModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
                } else if ($action == "Approve") {
                    $workOnHolidayModel->status = "RC";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved!!!");
                }
                $workOnHolidayModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                $workOnHolidayModel->id = $id;
                try {
                    HeadNotification::pushNotification(($workOnHolidayModel->status == 'RC') ? NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED : NotificationEvents::WORKONHOLIDAY_RECOMMEND_REJECTED, $workOnHolidayModel, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
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
                    $workOnHolidayModel->status = "AP";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved");
                }
                if ($role == 4) {
                    $workOnHolidayModel->recommendedBy = $this->employeeId;
                    $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $workOnHolidayModel->approvedRemarks = $getData->approvedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                $workOnHolidayModel->id = $id;
                try {
                    HeadNotification::pushNotification(($workOnHolidayModel->status == 'AP') ? NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED : NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED, $workOnHolidayModel, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        $holidays = $this->getHolidayList($requestedEmployeeID);
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
                    'holidays' => $holidays["holidayKVList"],
                    'holidayObjList' => $holidays["holidayList"]
        ]);
    }

    public function statusAction() {
        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => 'E'], Holiday::HOLIDAY_ENAME, "ASC", NULL, FALSE, TRUE);
        $holidays1 = [-1 => "All"] + $holidays;
        $holidayFormElement->setValueOptions($holidays1);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday Type");

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
                    'holidays' => $holidayFormElement,
                    'status' => $statusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getHolidayList($employeeId) {
        $holidayRepo = new HolidayRepository($this->adapter);
        $holidayResult = $holidayRepo->selectAll($employeeId);
        $holidayList = [];
        $holidayObjList = [];
        foreach ($holidayResult as $holidayRow) {
            //$todayDate = new \DateTime();
            $holidayList[$holidayRow['HOLIDAY_ID']] = $holidayRow['HOLIDAY_ENAME'] . " (" . $holidayRow['START_DATE'] . " to " . $holidayRow['END_DATE'] . ")";
            $holidayObjList[$holidayRow['HOLIDAY_ID']] = $holidayRow;
        }
        return ['holidayKVList' => $holidayList, 'holidayList' => $holidayObjList];
    }

}
