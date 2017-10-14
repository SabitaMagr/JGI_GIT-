<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
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
        $dayoffWorkRequest = $this->getAllList();
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
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
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
                try {
                    $workOnDayoffModel->id = $id;
                    HeadNotification::pushNotification(($workOnDayoffModel->status == 'RC') ? NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED : NotificationEvents::WORKONDAYOFF_RECOMMEND_REJECTED, $workOnDayoffModel, $this->adapter, $this);
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
                    try {
                        $this->wodApproveAction($detail);
                        $this->flashmessenger()->addMessage("Work on Day-off Request Approved");
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage("Work on Day-off Request Approved but reward is not provided as employee position is not set.");
                    }
                    $workOnDayoffModel->status = "AP";
                }
                if ($role == 4) {
                    $workOnDayoffModel->recommendedBy = $this->employeeId;
                    $workOnDayoffModel->recommendedDate = Helper::getcurrentExpressionDate();
                }

                $workOnDayoffModel->approvedRemarks = $getData->approvedRemarks;
                $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);

                try {
                    $workOnDayoffModel->id = $id;
                    HeadNotification::pushNotification(($workOnDayoffModel->status == 'AP') ? NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED : NotificationEvents::WORKONDAYOFF_APPROVE_REJECTED, $workOnDayoffModel, $this->adapter, $this);
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

    private function wodApproveAction($detail) {
        $this->dayoffWorkApproveRepository->wodReward($detail['ID']);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            if (!$request->ispost()) {
                throw new Exception('the request is not post');
            }
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
                foreach ($postData as $data) {
                    $workOnDayoffModel = new WorkOnDayoff();
                    $id = $data['id'];
                    $role = $data['role'];
                    $detail = $this->dayoffWorkApproveRepository->fetchById($id);

                    if ($role == 2) {
                        $workOnDayoffModel->recommendedDate = Helper::getcurrentExpressionDate();
                        $workOnDayoffModel->recommendedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $workOnDayoffModel->status = "R";
                        } else if ($action == "Approve") {
                            $workOnDayoffModel->status = "RC";
                        }
                        $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);
                        try {
                            $workOnDayoffModel->id = $id;
                            HeadNotification::pushNotification(($workOnDayoffModel->status == 'RC') ? NotificationEvents::WORKONDAYOFF_RECOMMEND_ACCEPTED : NotificationEvents::WORKONDAYOFF_RECOMMEND_REJECTED, $workOnDayoffModel, $this->adapter, $this);
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    } else if ($role == 3 || $role == 4) {
                        $workOnDayoffModel->approvedDate = Helper::getcurrentExpressionDate();
                        $workOnDayoffModel->approvedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $workOnDayoffModel->status = "R";
                        } else if ($action == "Approve") {
                            try {
                                $this->wodApproveAction($detail);
                            } catch (Exception $e) {
                                
                            }
                            $workOnDayoffModel->status = "AP";
                        }
                        if ($role == 4) {
                            $workOnDayoffModel->recommendedBy = $this->employeeId;
                            $workOnDayoffModel->recommendedDate = Helper::getcurrentExpressionDate();
                        }

                        $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);

                        try {
                            $workOnDayoffModel->id = $id;
                            HeadNotification::pushNotification(($workOnDayoffModel->status == 'AP') ? NotificationEvents::WORKONDAYOFF_APPROVE_ACCEPTED : NotificationEvents::WORKONDAYOFF_APPROVE_REJECTED, $workOnDayoffModel, $this->adapter, $this);
                        } catch (Exception $e) {
                            $this->flashmessenger()->addMessage($e->getMessage());
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
        $list = $this->dayoffWorkApproveRepository->getAllRequest($this->employeeId);
        return Helper::extractDbData($list);
    }

}
