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
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

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
        $statusSE = $this->getStatusSelectElement(['name' => 'travelStatus', "id" => "travelRequestStatusId", "class" => "form-control", 'label' => 'status']);
        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $statusSE,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequest = new TravelRequest();

        $detail = $this->travelApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];

        $recommApprove = $detail['RECOMMENDER_ID'] == $detail['APPROVER_ID'] ? 1 : 0;
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];


        $request = $this->getRequest();
        if ($request->isPost()) {
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

        $travelRequest->exchangeArrayFromDB((array) $detail);
        $this->form->bind($travelRequest);
        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        $transportTypes = array(
            'AP' => 'Aero Plane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus'
        );
        $vehicle = '';
        foreach ($transportTypes as $key => $value) {
            if ($detail['TRANSPORT_TYPE'] == $key) {
                $vehicle = $value;
            }
        }
        $empRepository = new EmployeeRepository($this->adapter);
        $empDtl = $empRepository->fetchForProfileById($detail['EMPLOYEE_ID']);

        $numberInWord = new NumberHelper();
        try {
            $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        } catch (Exception $e) {
            $advanceAmount = "";
        }
        $subDetail = [];
        if ($detail['SUB_EMPLOYEE_ID'] != null) {
            $subEmpDetail = $empRepository->fetchForProfileById($detail['SUB_EMPLOYEE_ID']);
            $subDetail = [
                'SUB_EMPLOYEE_NAME' => $detail['SUB_EMPLOYEE_ID'],
                'SUB_DESIGNATION' => $subEmpDetail['DESIGNATION'],
                'SUB_APPROVED_DATE' => $detail['SUB_APPROVED_DATE']
            ];
        }
        $duration = Helper::dateDiff($detail['FROM_DATE'], $detail['TO_DATE']) + 1;
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'requestType' => $requestType,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'approvedDT' => $detail['APPROVED_DATE'],
                    'status' => $status,
                    'transportTypes' => $transportTypes,
                    'recommApprove' => $recommApprove,
                    'subEmployeeId' => $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks' => $detail['SUB_REMARKS'],
                    'subApprovedFlag' => $detail['SUB_APPROVED_FLAG'],
                    'empDtl' => $empDtl,
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'vehicle' => $vehicle,
                    'advanceAmount' => $advanceAmount,
                    'subDetail' => $subDetail,
                    'duration' => $duration,
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", false, true)
        ]);
    }

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }

        $detail = $this->travelApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $recommApprove = $detail['RECOMMENDER_ID'] == $detail['APPROVER_ID'] ? 1 : 0;

        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];

        if ($detail['REFERENCE_TRAVEL_ID'] != null) {
            $referenceTravelDtl = $this->travelApproveRepository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        } else {
            $advanceAmt = 0;
        }
        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $expenseDtlRepo->fetchByTravelId($id);
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
        $totalExpense = $numberInWord->toText($totalAmount);

        $empRepository = new EmployeeRepository($this->adapter);
        $empDtl = $empRepository->fetchForProfileById($detail['EMPLOYEE_ID']);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'approvedDT' => $detail['APPROVED_DATE'],
                    'status' => $status,
                    'advanceAmt' => $advanceAmt,
                    'recommApprove' => $recommApprove,
                    'expenseDtlList' => $expenseDtlList,
                    'transportType' => $transportType,
                    'todayDate' => date('d-M-Y'),
                    'detail' => $detail,
                    'empDtl' => $empDtl,
                    'totalExpense' => $totalExpense
        ]);
    }

}
