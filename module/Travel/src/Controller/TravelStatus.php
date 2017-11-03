<?php

namespace Travel\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use ManagerService\Repository\TravelApproveRepository;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest;
use SelfService\Repository\TravelExpenseDtlRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TravelStatus extends AbstractActionController {

    private $adapter;
    private $travelApproveRepository;
    private $travelStatusRepository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $this->travelStatusRepository = new TravelStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $travelStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C' => 'Cancelled'
        ];
        $travelStatusFormElement = new Select();
        $travelStatusFormElement->setName("travelStatus");
        $travelStatusFormElement->setValueOptions($travelStatus);
        $travelStatusFormElement->setAttributes(["id" => "travelRequestStatusId", "class" => "form-control"]);
        $travelStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $travelStatusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequest = new TravelRequest();
        $request = $this->getRequest();

        $detail = $this->travelApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];

        $recommApprove = $detail['RECOMMENDER_ID'] == $detail['APPROVER_ID'] ? 1 : 0;
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];


        if (!$request->isPost()) {
            $travelRequest->exchangeArrayFromDB((array) $detail);
            $this->form->bind($travelRequest);
        } else {
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
        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        if ($detail['REFERENCE_TRAVEL_ID'] != null) {
            $referenceTravelDtl = $this->travelApproveRepository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        } else {
            $advanceAmt = 0;
        }
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
                    'advanceAmt' => $advanceAmt,
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
        $this->initializeForm();

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
