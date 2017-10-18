<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use Exception;
use ManagerService\Repository\AttendanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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
        $attendanceApprove = $this->getAllList();
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


        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $attendanceRequestRepository->fetchById($id);

        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];

        $approvedDT = $detail['APPROVED_DT'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];

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
                    $this->flashmessenger()->addMessage("Attendance Request Recommended!!!");
                }
                $model->recommendedRemarks = $getData->recommendedRemarks;
                $this->repository->edit($model, $id);

                try {
                    $model->id = $id;
                    HeadNotification::pushNotification(($model->status == 'RC') ? NotificationEvents::ATTENDANCE_RECOMMEND_ACCEPTED : NotificationEvents::ATTENDANCE_RECOMMEND_REJECTED, $model, $this->adapter, $this);
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
                    HeadNotification::pushNotification(($model->status == 'AP') ? NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED : NotificationEvents::ATTENDANCE_APPROVE_REJECTED, $model, $this->adapter, $this);
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

            if ($postData == null) {
                throw new Exception('no selected rows');
            } else {
                $this->adapter->getDriver()->getConnection()->beginTransaction();
                try {
                    //start of loop
                    $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);
                    foreach ($postData as $data) {
                        $id = $data['id'];
                        $role = $data['role'];

                        $detail = $attendanceRequestRepository->fetchById($id);
                        $model = new AttendanceRequestModel();

                        if ($role == 2) {
                            $model->recommendedDate = Helper::getcurrentExpressionDate();
                            $model->recommendedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $model->status = "R";
                            } else if ($action == "Approve") {
                                $model->status = "RC";
                            }
                            $this->repository->edit($model, $id);
                            $model->id = $id;
                            try {
                                HeadNotification::pushNotification(($model->status == 'RC') ? NotificationEvents::ATTENDANCE_RECOMMEND_ACCEPTED : NotificationEvents::ATTENDANCE_RECOMMEND_REJECTED, $model, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        } else if ($role == 3 || $role == 4) {
                            $model->approvedDt = Helper::getcurrentExpressionDate();
                            $model->approvedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $model->status = "R";
                            } else if ($action == "Approve") {
                                $model->status = "AP";
                            }
                            if ($role == 4) {
                                $model->recommendedDate = Helper::getcurrentExpressionDate();
                                $model->recommendedBy = $this->employeeId;
                            }
                            $this->repository->edit($model, $id);
                            $this->repository->backdateAttendance(Helper::getExpressionDate($detail['ATTENDANCE_DT']), $detail['EMPLOYEE_ID'], Helper::getExpressionTime($detail['IN_TIME']), Helper::getExpressionTime($detail['OUT_TIME']));

                            $model->id = $id;
                            try {
                                HeadNotification::pushNotification(($model->status == 'AP') ? NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED : NotificationEvents::ATTENDANCE_APPROVE_REJECTED, $model, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                    //end of loop
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
        return Helper::extractDbData($list);
    }

    public function pullAttendanceRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $attendanceStatusRepository = new AttendanceStatusRepository($this->adapter);
            if (key_exists('approverId', $data)) {
                $approverId = $data['approverId'];
            } else {
                $approverId = null;
            }
            $result = $attendanceStatusRepository->getFilteredRecord($data, $approverId);

            $recordList = [];
            $getValue = function($status) {
                if ($status == "RQ") {
                    return "Pending";
                } else if ($status == "R") {
                    return "Rejected";
                } elseif ($status == "RC") {
                    return "Recommended";
                } else if ($status == "AP") {
                    return "Approved";
                } else if ($status == "C") {
                    return "Cancelled";
                }
            };

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
            foreach ($result as $row) {

                $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
                $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

                $status = $getValue($row['STATUS']);
                $statusId = $row['STATUS'];
                $approvedDT = $row['APPROVED_DT'];

                $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
                $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

                $roleID = $getRole($authRecommender, $authApprover, $approverId);
                $recommenderName = $fullName($authRecommender);
                $approverName = $fullName($authApprover);

                $role = [
                    'APPROVER_NAME' => $approverName,
                    'RECOMMENDER_NAME' => $recommenderName,
                    'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $approverId),
                    'ROLE' => $roleID
                ];
                if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                    $role['YOUR_ROLE'] = 'RECOMMENDER/APPROVER';
                    $role['ROLE'] = 4;
                }
                $new_row = array_merge($row, ['STATUS' => $status]);
                $final_record = array_merge($new_row, $role);

                array_push($recordList, $final_record);
            }


            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "num" => count($recordList)
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
