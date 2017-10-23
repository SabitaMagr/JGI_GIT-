<?php

namespace ManagerService\Controller;

use Advance\Repository\AdvanceStatusRepository;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\AdvanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest;
use Setup\Model\Advance;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AdvanceApproveController extends AbstractActionController {

    private $advanceApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->advanceApproveRepository = new AdvanceApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new AdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $advanceApprove = $this->getAllList();
        return Helper::addFlashMessagesToArray($this, ['advanceApprove' => $advanceApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("advanceApprove");
        }
        $advanceRequestModel = new AdvanceRequest();
        $request = $this->getRequest();

        $detail = $this->advanceApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $recommender = $detail['RECOMMENDER_NAME'];
        $approver = $detail['APPROVER_NAME'];

        $recommended_by = $detail['RECOMMENDED_BY_NAME'];
        $approved_by = $detail['APPROVED_BY_NAME'];
        $authRecommender = ($recommended_by != null) ? $recommender : $recommended_by;
        $authApprover = $approved_by != null ? $approver : $approved_by;

        $recommenderId = ($detail['RECOMMENDED_BY'] != null) ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
        if (!$request->isPost()) {
            $advanceRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($advanceRequestModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;
            if ($role == 2) {
                $advanceRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $advanceRequestModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $advanceRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $advanceRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Advance Request Approved!!!");
                }
                $advanceRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->advanceApproveRepository->edit($advanceRequestModel, $id);

                try {
                    $advanceRequestModel->advanceRequestId = $id;
                    HeadNotification::pushNotification(($advanceRequestModel->status == 'RC') ? NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED : NotificationEvents::ADVANCE_RECOMMEND_REJECTED, $advanceRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $advanceRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $advanceRequestModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $advanceRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
                } else if ($action == "Approve") {
                    $advanceRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Advance Request Approved");
                }
                if ($role == 4) {
                    $advanceRequestModel->recommendedBy = $this->employeeId;
                    $advanceRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $advanceRequestModel->approvedRemarks = $getData->approvedRemarks;
                $this->advanceApproveRepository->edit($advanceRequestModel, $id);

                try {
                    $advanceRequestModel->advanceRequestId = $id;
                    HeadNotification::pushNotification(($advanceRequestModel->status == 'AP') ? NotificationEvents::ADVANCE_APPROVE_ACCEPTED : NotificationEvents::ADVANCE_APPROVE_REJECTED, $advanceRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("advanceApprove");
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
                    'advances' => EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => "E"], Advance::ADVANCE_ID, "ASC", NULL, FALSE, TRUE)
        ]);
    }

    public function statusAction() {
        $advanceFormElement = new Select();
        $advanceFormElement->setName("advance");
        $advances = EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => 'E'], Advance::ADVANCE_NAME, "ASC", NULL, FALSE, TRUE);
        $advances1 = [-1 => "All"] + $advances;
        $advanceFormElement->setValueOptions($advances1);
        $advanceFormElement->setAttributes(["id" => "advanceId", "class" => "form-control"]);
        $advanceFormElement->setLabel("Advance Type");

        $advanceStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $advanceStatusFormElement = new Select();
        $advanceStatusFormElement->setName("advanceStatus");
        $advanceStatusFormElement->setValueOptions($advanceStatus);
        $advanceStatusFormElement->setAttributes(["id" => "advanceRequestStatusId", "class" => "form-control"]);
        $advanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'advances' => $advanceFormElement,
                    'advanceStatus' => $advanceStatusFormElement,
                    'recomApproveId' => $this->employeeId,
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

                    foreach ($postData as $data) {
                        $advanceRequestModel = new AdvanceRequest();
                        $id = $data['id'];
                        $role = $data['role'];

                        if ($role == 2) {
                            $advanceRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            $advanceRequestModel->recommendedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $advanceRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $advanceRequestModel->status = "RC";
                            }
                            $this->advanceApproveRepository->edit($advanceRequestModel, $id);

                            try {
                                $advanceRequestModel->advanceRequestId = $id;
                                HeadNotification::pushNotification(($advanceRequestModel->status == 'RC') ? NotificationEvents::ADVANCE_RECOMMEND_ACCEPTED : NotificationEvents::ADVANCE_RECOMMEND_REJECTED, $advanceRequestModel, $this->adapter, $this);
                            } catch (Exception $e) {
                                $this->flashmessenger()->addMessage($e->getMessage());
                            }
                        } else if ($role == 3 || $role == 4) {
                            $advanceRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                            $advanceRequestModel->approvedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $advanceRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $advanceRequestModel->status = "AP";
                            }
                            if ($role == 4) {
                                $advanceRequestModel->recommendedBy = $this->employeeId;
                                $advanceRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            }
                            $this->advanceApproveRepository->edit($advanceRequestModel, $id);

                            try {
                                $advanceRequestModel->advanceRequestId = $id;
                                HeadNotification::pushNotification(($advanceRequestModel->status == 'AP') ? NotificationEvents::ADVANCE_APPROVE_ACCEPTED : NotificationEvents::ADVANCE_APPROVE_REJECTED, $advanceRequestModel, $this->adapter, $this);
                            } catch (Exception $e) {
                                $this->flashmessenger()->addMessage($e->getMessage());
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
        $list = $this->advanceApproveRepository->getAllRequest($this->employeeId);
        return Helper::extractDbData($list);
    }

    public function pullAdvanceRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $advanceStatusRepository = new AdvanceStatusRepository($this->adapter);
            if (key_exists('recomApproveId', $data)) {
                $recomApproveId = $data['recomApproveId'];
            } else {
                $recomApproveId = null;
            }
            $result = $advanceStatusRepository->getFilteredRecord($data, $recomApproveId);

            $recordList = Helper::extractDbData($result);

            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
