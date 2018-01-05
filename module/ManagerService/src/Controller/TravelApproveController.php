<?php

namespace ManagerService\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TravelApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest;
use SelfService\Repository\TravelExpenseDtlRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TravelApproveController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TravelApproveRepository::class);
        $this->initializeForm(TravelRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $search['employeeId'] = $this->employeeId;
                $search['status'] = ['RQ', 'RC'];
                $rawList = $this->repository->getAllFiltered($search);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');
        if ($id === 0 || $role === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $request = $this->getRequest();
        $travelRequestModel = new TravelRequest();
        if ($request->isPost()) {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $travelRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $travelRequestModel->recommendedBy = (int) $this->employeeId;
                if ($action == "Reject") {
                    $travelRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
                } else if ($action == "Approve") {
                    $travelRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Travel Request Approved!!!");
                }
                $travelRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->repository->edit($travelRequestModel, $id);
                $travelRequestModel->travelId = $id;
                try {
                    HeadNotification::pushNotification(($travelRequestModel->status == 'RC') ? NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED : NotificationEvents::TRAVEL_RECOMMEND_REJECTED, $travelRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $travelRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $travelRequestModel->approvedBy = (int) $this->employeeId;
                if ($action == "Reject") {
                    $travelRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
                } else if ($action == "Approve") {
                    $travelRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Travel Request Approved");
                }
                if ($role == 4) {
                    $travelRequestModel->recommendedBy = $this->employeeId;
                    $travelRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                }

                $travelRequestModel->approvedRemarks = $getData->approvedRemarks;
                $this->repository->edit($travelRequestModel, $id);
                $travelRequestModel->travelId = $id;

                try {
                    HeadNotification::pushNotification(($travelRequestModel->status == 'AP') ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED, $travelRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("travelApprove");
        }

        $detail = $this->repository->fetchById($id);
        $travelRequestModel->exchangeArrayFromDB($detail);
        $this->form->bind($travelRequestModel);

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'role' => $role,
                    'form' => $this->form,
                    'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
                    'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
        ]);
    }

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $detail = $this->repository->fetchById($id);

        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];


        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $result = $expenseDtlRepo->fetchByTravelId($id);
        $expenseDtlList = [];
        $totalAmount = 0;
        foreach ($result as $row) {
            $totalAmount += $row['TOTAL_AMOUNT'];
            array_push($expenseDtlList, $row);
        }
        $transportType = [
            "AP" => "Aero Plane",
            "OV" => "Office Vehicles",
            "TI" => "Taxi",
            "BS" => "Bus"
        ];
        $numberInWord = new NumberHelper();
        $totalAmountInWords = $numberInWord->toText($totalAmount);
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'recommendedBy' => $recommenderId,
                    'employeeId' => $this->employeeId,
                    'expenseDtlList' => $expenseDtlList,
                    'transportType' => $transportType,
                    'todayDate' => date('d-M-Y'),
                    'detail' => $detail,
                    'totalAmount' => $totalAmount,
                    'totalAmountInWords' => $totalAmountInWords,
                    'balance' => $balance
                        ]
        );
    }

    public function statusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                $searchQuery['employeeId'] = $this->employeeId;
                $rawList = $this->repository->getAllFiltered((array) $searchQuery);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'travelStatus' => $statusSE,
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
                        $travelRequestModel = new TravelRequest();
                        $id = $data['id'];
                        $role = $data['role'];
                        $detail = $this->repository->fetchById($id);

                        if ($role == 2) {
                            $travelRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            $travelRequestModel->recommendedBy = (int) $this->employeeId;
                            if ($action == "Reject") {
                                $travelRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $travelRequestModel->status = "RC";
                            }
                            $this->repository->edit($travelRequestModel, $id);
                            $travelRequestModel->travelId = $id;
                            try {
                                HeadNotification::pushNotification(($travelRequestModel->status == 'RC') ? NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED : NotificationEvents::TRAVEL_RECOMMEND_REJECTED, $travelRequestModel, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        } else if ($role == 3 || $role == 4) {
                            $travelRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                            $travelRequestModel->approvedBy = (int) $this->employeeId;
                            if ($action == "Reject") {
                                $travelRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $travelRequestModel->status = "AP";
                            }
                            if ($role == 4) {
                                $travelRequestModel->recommendedBy = $this->employeeId;
                                $travelRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            }

                            $this->repository->edit($travelRequestModel, $id);
                            $travelRequestModel->travelId = $id;


                            try {
                                HeadNotification::pushNotification(($travelRequestModel->status == 'AP') ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED, $travelRequestModel, $this->adapter, $this);
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

}
