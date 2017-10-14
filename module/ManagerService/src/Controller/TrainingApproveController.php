<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use DateTime;
use Exception;
use ManagerService\Repository\TrainingApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TrainingRequestForm;
use SelfService\Model\TrainingRequest;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\TrainingRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingApproveController extends AbstractActionController {

    private $trainingApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->trainingApproveRepository = new TrainingApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $trainingApprove = $this->getAllList();
        return Helper::addFlashMessagesToArray($this, ['trainingApprove' => $trainingApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("trainingApprove");
        }
        $trainingRequestModel = new TrainingRequest();
        $request = $this->getRequest();

        $detail = $this->trainingApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
        if ($detail['TRAINING_ID'] != 0) {
            $detail['START_DATE'] = $detail['T_START_DATE'];
            $detail['END_DATE'] = $detail['T_END_DATE'];
            $detail['DURATION'] = $detail['T_DURATION'];
            $detail['TRAINING_TYPE'] = $detail['T_TRAINING_TYPE'];
        }
        if (!$request->isPost()) {
            $trainingRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($trainingRequestModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;
            if ($role == 2) {
                $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $trainingRequestModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $trainingRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Training Request Rejected!!!");
                } else if ($action == "Approve") {
                    $trainingRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Training Request Approved!!!");
                }
                $trainingRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                $trainingRequestModel->requestId = $id;
                try {
                    HeadNotification::pushNotification(($trainingRequestModel->status == 'RC') ? NotificationEvents::TRAINING_RECOMMEND_ACCEPTED : NotificationEvents::TRAINING_RECOMMEND_REJECTED, $trainingRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $trainingRequestModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $trainingRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Training Request Rejected!!!");
                } else if ($action == "Approve") {
                    $trainingRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Training Request Approved");
                }
                if ($role == 4) {
                    $trainingRequestModel->recommendedBy = $this->employeeId;
                    $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                }

                // to update back date changes
                $sDate = $detail['START_DATE'];
                $eDate = $detail['END_DATE'];
                $trainingId = $detail['TRAINING_ID'];
                $currDate = Helper::getCurrentDate();
                $begin = new DateTime($sDate);
                $end = new DateTime($eDate);
                $attendanceDetailModel = new AttendanceDetail();
                $attendanceDetailModel->trainingId = $trainingId;
                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);

                //                start of transaction
                $connection = $this->adapter->getDriver()->getConnection();
                $connection->beginTransaction();
                try {
                    if (strtotime($sDate) <= strtotime($currDate)) {
                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                            $trainingdate = $i->format("d-M-Y");
                            if (strtotime($trainingdate) <= strtotime($currDate)) {
                                $where = ["EMPLOYEE_ID" => $requestedEmployeeID, "ATTENDANCE_DT" => $trainingdate];
                                $attendanceDetailRepo->editWith($attendanceDetailModel, $where);
                            }
                        }
                    }
                    $trainingRequestModel->approvedRemarks = $getData->approvedRemarks;
                    $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                    $trainingRequestModel->requestId = $id;
                    $connection->commit();
                } catch (exception $e) {
                    $connection->rollback();
                    echo "error message:" . $e->getMessage();
                }
//                end of transaction
                try {
                    HeadNotification::pushNotification(($trainingRequestModel->status == 'AP') ? NotificationEvents::TRAINING_APPROVE_ACCEPTED : NotificationEvents::TRAINING_APPROVE_REJECTED, $trainingRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("trainingApprove");
        }
        $trainingTypes = array(
            'CP' => 'Company Personal',
            'CC' => 'Company Contribution'
        );
        $trainings = $this->getTrainingList($requestedEmployeeID);
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
                    'trainingIdSelected' => $detail['TRAINING_ID'],
                    'trainings' => $trainings["trainingKVList"],
                    'trainingTypes' => $trainingTypes,
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

    public function getTrainingList($employeeId) {
        $trainingRepo = new TrainingRepository($this->adapter);
        $trainingResult = $trainingRepo->selectAll($employeeId);
        $trainingList = [];
        $allTrainings = [];
        foreach ($trainingResult as $trainingRow) {
            $trainingList[$trainingRow['TRAINING_ID']] = $trainingRow['TRAINING_NAME'] . " (" . $trainingRow['START_DATE'] . " to " . $trainingRow['END_DATE'] . ")";
            $allTrainings[$trainingRow['TRAINING_ID']] = $trainingRow;
        }
        return ['trainingKVList' => $trainingList, 'trainingList' => $allTrainings];
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

                    foreach ($postData as $data) {
                        $id = $data['id'];
                        $role = $data['role'];
                        $trainingRequestModel = new TrainingRequest();
//                        $detail = $this->trainingApproveRepository->fetchById($id);

                        if ($role == 2) {
                            $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            $trainingRequestModel->recommendedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $trainingRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $trainingRequestModel->status = "RC";
                            }
                            $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                            $trainingRequestModel->requestId = $id;
                            try {
                                HeadNotification::pushNotification(($trainingRequestModel->status == 'RC') ? NotificationEvents::TRAINING_RECOMMEND_ACCEPTED : NotificationEvents::TRAINING_RECOMMEND_REJECTED, $trainingRequestModel, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        } else if ($role == 3 || $role == 4) {
                            $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                            $trainingRequestModel->approvedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $trainingRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $trainingRequestModel->status = "AP";
                            }
                            if ($role == 4) {
                                $trainingRequestModel->recommendedBy = $this->employeeId;
                                $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            }
                            $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                            $trainingRequestModel->requestId = $id;
                            try {
                                HeadNotification::pushNotification(($trainingRequestModel->status == 'AP') ? NotificationEvents::TRAINING_APPROVE_ACCEPTED : NotificationEvents::TRAINING_APPROVE_REJECTED, $trainingRequestModel, $this->adapter, $this);
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
        $list = $this->trainingApproveRepository->getAllRequest($this->employeeId);
        return Helper::extractDbData($list);
    }

}
