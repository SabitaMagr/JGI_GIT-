<?php

namespace Travel\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TravelApproveRepository;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest;
use SelfService\Repository\TravelExpenseDtlRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TravelStatus extends HrisController {

    private $travelApproveRepository;
    private $travelStatusRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(TravelRequestForm::class);
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $this->travelStatusRepository = new TravelStatusRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $search = $request->getPost();
                $rawList = $this->travelStatusRepository->getFilteredRecord($search);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $statusSE = $this->getStatusSelectElement(['name' => 'status', "id" => "status", "class" => "form-control", 'label' => 'status']);
        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $statusSE,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function actionAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $travelRequest = new TravelRequest();
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $travelRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $travelRequest->status = "R";
                $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
            } else if ($action == "Approve") {
                $travelRequest->status = "AP";
                $this->flashmessenger()->addMessage("Travel Request Approved");
            }
            $travelRequest->approvedBy = $this->employeeId;
            $travelRequest->approvedRemarks = $reason;
            $this->travelApproveRepository->edit($travelRequest, $id);

            return $this->redirect()->toRoute("travelStatus");
        }
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequestModel = new TravelRequest();
        $detail = $this->travelApproveRepository->fetchById($id);
        $travelRequestModel->exchangeArrayFromDB($detail);
        $this->form->bind($travelRequestModel);

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
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
        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $detail = $this->travelApproveRepository->fetchById($id);

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

    public function settlementReportAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $list = $this->travelStatusRepository->notSettled();
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return [];
    }

}
