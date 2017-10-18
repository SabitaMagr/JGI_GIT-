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
use LeaveManagement\Repository\LeaveStatusRepository;
use ManagerService\Repository\LeaveApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Repository\LeaveRequestRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
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
            }
            $listData = $this->getAllList();
            return new CustomViewModel(['success' => true, 'data' => $listData]);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getAllList() {
        $list = $this->repository->getAllRequest($this->employeeId);
        return Helper::extractDbData($list);
    }

    public function pullLeaveRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $leaveStatusRepository = new LeaveStatusRepository($this->adapter);
            if (key_exists('recomApproveId', $data)) {
                $recomApproveId = $data['recomApproveId'];
            } else {
                $recomApproveId = null;
            }
            $result = $leaveStatusRepository->getFilteredRecord($data, $recomApproveId);

            $recordList = [];
            $getRoleDtl = function($recommender, $approver, $recomApproveId) {
                if ($recomApproveId == $recommender) {
                    return 'RECOMMENDER';
                } else if ($recomApproveId == $approver) {
                    return 'APPROVER';
                } else {
                    return null;
                }
            };
            $getRole = function($recommender, $approver, $recomApproveId) {
                if ($recomApproveId == $recommender) {
                    return 2;
                } else if ($recomApproveId == $approver) {
                    return 3;
                } else {
                    return null;
                }
            };
            $fullName = function($id) {
                $empRepository = new EmployeeRepository($this->adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            };

            $getValue = function($status) {
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

            foreach ($result as $row) {
                $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
                $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

                $status = $getValue($row['STATUS']);
                $statusId = $row['STATUS'];
                $approvedDT = $row['APPROVED_DT'];

                $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
                $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

                $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
                $recommenderName = $fullName($authRecommender);
                $approverName = $fullName($authApprover);

                $role = [
                    'APPROVER_NAME' => $approverName,
                    'RECOMMENDER_NAME' => $recommenderName,
                    'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                    'ROLE' => $roleID
                ];
                if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                    $role['YOUR_ROLE'] = 'Recommender\Approver';
                    $role['ROLE'] = 4;
                }
                $new_row = array_merge($row, ['STATUS' => $status]);
                $final_record = array_merge($new_row, $role);
                array_push($recordList, $final_record);
            }

            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "num" => count($recordList),
                "recomApproveId" => $recomApproveId
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
