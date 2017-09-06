<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Repository\LeaveApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Repository\LeaveRequestRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveApproveController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new LeaveApproveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
        $leaveApprove = $this->getAllList();
        return Helper::addFlashMessagesToArray($this, ['leaveApprove' => $leaveApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveapprove");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

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
        //to get the previous balance of selected leave from assigned leave detail
        $result = $this->repository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if (!$request->isPost()) {
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                if ($action == "Reject") {
                    $leaveApply->status = "R";
                    $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
                } else if ($action == "Approve") {
                    $leaveApply->status = "RC";
                    $this->flashmessenger()->addMessage("Leave Request Approved!!!");
                }
                $leaveApply->recommendedBy = $this->employeeId;
                $leaveApply->recommendedRemarks = $getData->recommendedRemarks;
                $this->repository->edit($leaveApply, $id);

                $leaveApply->id = $id;
                $leaveApply->employeeId = $requestedEmployeeID;
                $leaveApply->approvedBy = $detail['APPROVER'];
                try {
                    if ($leaveApply->status == 'RC') {
                        HeadNotification::pushNotification(NotificationEvents::LEAVE_RECOMMEND_ACCEPTED, $leaveApply, $this->adapter, $this);
                    } else {
                        HeadNotification::pushNotification(NotificationEvents::LEAVE_RECOMMEND_REJECTED, $leaveApply, $this->adapter, $this);
                    }
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
                if ($action == "Reject") {
                    $leaveApply->status = "R";
                    $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
                } else if ($action == "Approve") {
                    $leaveApply->status = "AP";
                    $this->flashmessenger()->addMessage("Leave Request Approved");
                }
                unset($leaveApply->halfDay);
                $leaveApply->approvedBy = $this->employeeId;
                $leaveApply->approvedRemarks = $getData->approvedRemarks;

                if ($role == 4) {
                    $leaveApply->recommendedBy = $this->employeeId;
                    $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                }
                $this->repository->edit($leaveApply, $id);

                $leaveApply->id = $id;
                $leaveApply->employeeId = $requestedEmployeeID;



                try {
                    HeadNotification::pushNotification(($leaveApply->status == 'AP') ? NotificationEvents::LEAVE_APPROVE_ACCEPTED : NotificationEvents::LEAVE_APPROVE_REJECTED, $leaveApply, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("leaveapprove");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DT'],
                    'role' => $role,
                    'availableDays' => $preBalance,
                    'status' => $detail['STATUS'],
                    'remarksDtl' => $detail['REMARKS'],
                    'totalDays' => $result['TOTAL_DAYS'],
                    'recommendedBy' => $recommenderId,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'approvedDT' => $detail['APPROVED_DT'],
                    'employeeId' => $this->employeeId,
                    'requestedEmployeeId' => $requestedEmployeeID,
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
                    'customRenderer' => Helper::renderCustomView(),
                    'subEmployeeId' => $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks' => $detail['SUB_REMARKS'],
                    'subApprovedFlag' => $detail['SUB_APPROVED_FLAG'],
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", FALSE, TRUE)
                    , 'gp' => $detail['GRACE_PERIOD']
        ]);
    }

    public function statusAction() {
        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaves = EntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E'], LeaveMaster::LEAVE_ENAME, "ASC", NULL, FALSE, TRUE);
        $leaves1 = [-1 => "All"] + $leaves;
        $leaveFormElement->setValueOptions($leaves1);
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control"]);
        $leaveFormElement->setLabel("Type");

        $leaveStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $leaveStatusFormElement = new Select();
        $leaveStatusFormElement->setName("leaveStatus");
        $leaveStatusFormElement->setValueOptions($leaveStatus);
        $leaveStatusFormElement->setAttributes(["id" => "leaveRequestStatusId", "class" => "form-control"]);
        $leaveStatusFormElement->setLabel("Status");



        return Helper::addFlashMessagesToArray($this, [
                    'leaves' => $leaveFormElement,
                    'leaveStatus' => $leaveStatusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            if (!$request->ispost()) {
                throw new Exception('the request is not post');
            }
            $action;
            $postData = $request->getPost()['data'];
            $postBtnAction = $request->getPost()['btnAction'];
            if ($postBtnAction == 'btnApprove') {
                $action = 'Approve';
            } elseif ($postBtnAction == 'btnReject') {
                $action = 'Reject';
            } else {
                throw new Exception('no action defined');
            }
//            print_r($action);
//            die();

            if ($postData == null) {
                throw new Exception('no selected rows');
            } else {
//                print_r($action);
                $this->adapter->getDriver()->getConnection()->beginTransaction();

                try {
                    foreach ($postData as $data) {
                        $leaveApply = new LeaveApply();
                        $id = $data['id'];
                        $role = $data['role'];

                        $detail = $this->repository->fetchById($id);
                        $requestedEmployeeID = $detail['EMPLOYEE_ID'];

                        if ($role == 2) {
                            $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                            if ($action == "Reject") {
                                $leaveApply->status = "R";
                            } else if ($action == "Approve") {
                                $leaveApply->status = "RC";
                            }
                            $leaveApply->recommendedBy = $this->employeeId;
//                        $leaveApply->recommendedRemarks = $getData->recommendedRemarks;
                            $this->repository->edit($leaveApply, $id);


                            $leaveApply->id = $id;
                            $leaveApply->employeeId = $requestedEmployeeID;
                            $leaveApply->approvedBy = $detail['APPROVER'];

                            try {
                                if ($leaveApply->status == 'RC') {
                                    HeadNotification::pushNotification(NotificationEvents::LEAVE_RECOMMEND_ACCEPTED, $leaveApply, $this->adapter, $this);
                                } else {
                                    HeadNotification::pushNotification(NotificationEvents::LEAVE_RECOMMEND_REJECTED, $leaveApply, $this->adapter, $this);
                                }
                            } catch (Exception $e) {
                                
                            }
                        } else if ($role == 3 || $role == 4) {
                            $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
                            if ($action == "Reject") {
                                $leaveApply->status = "R";
                            } else if ($action == "Approve") {
                                $leaveApply->status = "AP";
                            }
                            unset($leaveApply->halfDay);
                            $leaveApply->approvedBy = $this->employeeId;
                            // $leaveApply->approvedRemarks = $getData->approvedRemarks;

                            if ($role == 4) {
                                $leaveApply->recommendedBy = $this->employeeId;
                                $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                            }
                            $this->repository->edit($leaveApply, $id);
                            $leaveApply->id = $id;
                            $leaveApply->employeeId = $requestedEmployeeID;
                            try {
                                HeadNotification::pushNotification(($leaveApply->status == 'AP') ? NotificationEvents::LEAVE_APPROVE_ACCEPTED : NotificationEvents::LEAVE_APPROVE_REJECTED, $leaveApply, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                    $this->adapter->getDriver()->getConnection()->commit();
                } catch (Exception $ex) {
                    $this->adapter->getDriver()->getConnection()->rollback();
                }
            }
            $listData = $this->getAllList();
            return new CustomViewModel(['success' => true, 'data' => $listData]);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getAllList() {
        $list = $this->repository->getAllRequest($this->employeeId);

        $leaveApprove = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getRole = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 2;
            } else if ($this->employeeId == $approver) {
                return 3;
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
        foreach ($list as $row) {
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            $dataArray = [
                'FULL_NAME' => $row['FULL_NAME'],
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'START_DATE' => $row['START_DATE'],
                'END_DATE' => $row['END_DATE'],
                'APPLIED_DATE' => $row['APPLIED_DATE'],
                'NO_OF_DAYS' => $row['NO_OF_DAYS'],
                'LEAVE_ENAME' => $row['LEAVE_ENAME'],
                'ID' => $row['ID'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER']),
                'APPLIED_DATE_N' => $row['APPLIED_DATE_N'],
                'START_DATE_N' => $row['START_DATE_N'],
                'END_DATE_N' => $row['END_DATE_N']
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($leaveApprove, $dataArray);
        }
        return $leaveApprove;
    }

}
