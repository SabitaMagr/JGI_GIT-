<?php

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Model\TravelSubstitute;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use SelfService\Repository\TravelSubstituteRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class TravelRequest extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
    private $recommender;
    private $approver;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TravelRequestRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function getRecommendApprover() {
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);
        if ($empRecommendApprove != null) {
            $this->recommender = $empRecommendApprove['RECOMMEND_BY'];
            $this->approver = $empRecommendApprove['APPROVED_BY'];
        } else {
            $result = $this->recommendApproveList();
            if (count($result['recommender']) > 0) {
                $this->recommender = $result['recommender'][0]['id'];
            } else {
                $this->recommender = null;
            }
            if (count($result['approver']) > 0) {
                $this->approver = $result['approver'][0]['id'];
            } else {
                $this->approver = null;
            }
        }
    }

    public function indexAction() {
        $this->getRecommendApprover();
        $result = $this->repository->getAllByEmployeeId($this->employeeId);
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);

        $list = [];
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
            } else if ($status == "SC") {
                return "Settlement Checked";
            }
        };
        $getAction = function($status) {
            if ($status == "RQ") {
                return ["delete" => 'Cancel Request'];
            } else {
                return ["view" => 'View'];
            }
        };
        $getRequestedType = function($requestedType) {
            if ($requestedType == 'ad') {
                return 'Advance';
            } else if ($requestedType == 'ep') {
                return 'Expense';
            }
        };
        foreach ($result as $row) {
            $status = $getValue($row['STATUS']);
            $action = $getAction($row['STATUS']);
            $statusID = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];
            $MN1 = ($row['MN1'] != null) ? " " . $row['MN1'] . " " : " ";
            $recommended_by = $row['FN1'] . $MN1 . $row['LN1'];
            $MN2 = ($row['MN2'] != null) ? " " . $row['MN2'] . " " : " ";
            $approved_by = $row['FN2'] . $MN2 . $row['LN2'];

            $recommenderName = $approverName;
            $empRepository = new EmployeeRepository($this->adapter);
            $approverFlag = ($row['APPROVER_ROLE'] == 'DCEO') ? [HrEmployees::IS_DCEO => 'Y'] : [HrEmployees::IS_CEO => 'Y'];
            $whereCondition = array_merge([HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], $approverFlag);
            $approverDetail = $empRepository->fetchByCondition($whereCondition);
            $approverName = ($approverDetail != null) ? $approverDetail['FIRST_NAME'] . " " . $approverDetail['MIDDLE_NAME'] . " " . $approverDetail['LAST_NAME'] : "";

            $authRecommender = ($statusID == 'RQ' || $statusID == 'C') ? $recommenderName : $recommended_by;
            $authApprover = ($statusID == 'RC' || $statusID == 'RQ' || $statusID == 'C' || ($statusID == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

            $new_row = array_merge($row, [
                'RECOMMENDER_NAME' => $authRecommender,
                'APPROVER_NAME' => $authApprover,
                'STATUS' => $status,
                'ACTION' => key($action),
                'REQUESTED_TYPE' => $getRequestedType($row['REQUESTED_TYPE']),
                'ACTION_TEXT' => $action[key($action)]
            ]);
            if (in_array($statusID, ['C', 'R'])) {
                $new_row['ALLOW_TO_EDIT'] = 0;
            } else {
                $new_row['ALLOW_TO_EDIT'] = 1;
            }
            $checkForExpense = $this->repository->fetchByReferenceId($row['TRAVEL_ID']);
            //print_r($checkForExpense); die();
            $new_row['ALLOW_TO_REQUEST_EX'] = ($statusID == 'AP' && $new_row['REQUESTED_TYPE'] == 'Advance' && count($checkForExpense) == 0) ? 1 : 0;
            array_push($list, $new_row);
        }
        //print_r($list); die();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $travelSubstitute = $postData->travelSubstitute;
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->requestedAmount = ($model->requestedAmount == null) ? 0 : $model->requestedAmount;
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $this->repository->add($model);
                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");

                if ($travelSubstitute != null && $travelSubstitute != "") {
                    $travelSubstituteModel = new TravelSubstitute();
                    $travelSubstituteRepo = new TravelSubstituteRepository($this->adapter);

                    $travelSubstitute = $postData->travelSubstitute;

                    $travelSubstituteModel->travelId = $model->travelId;
                    $travelSubstituteModel->employeeId = $travelSubstitute;
                    $travelSubstituteModel->createdBy = $this->employeeId;
                    $travelSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
                    $travelSubstituteModel->status = 'E';

                    $travelSubstituteRepo->add($travelSubstituteModel);
                    try {
                        HeadNotification::pushNotification(NotificationEvents::TRAVEL_SUBSTITUTE_APPLIED, $model, $this->adapter, $this);
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage($e->getMessage());
                    }
                } else {
                    try {
                        HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage($e->getMessage());
                    }
                }
                return $this->redirect()->toRoute("travelRequest");
            }
        }
        $requestType = array(
            'ad' => 'Advance'
        );
        $transportTypes = array(
            'AP' => 'Flight',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus'
        );
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'requestTypes' => $requestType,
                    'transportTypes' => $transportTypes,
                    'customRender' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", false, true)
        ]);
    }

    public function expenseRequestAction() {
        $request = $this->getRequest();
        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $expenseDtlList = $postData['data']['expenseDtlList'];
            $departureDate = $postData['data']['departureDate'];
            $returnedDate = $postData['data']['returnedDate'];
            $destination = $postData['data']['destination'];
            $purpose = $postData['data']['purpose'];
            $advanceAmount = $postData['data']['advanceAmount'];
            $requestedType = $postData['data']['requestedType'];
            $travelId = (int) $postData['data']['travelId'];
            $sumAllTotal = (float) $postData['data']['sumAllTotal'];
            $approverRole = $postData['data']['approverRole'];
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlModel = new TravelExpenseDetail();

            $requestedAmt = $sumAllTotal;
            $model->fromDate = Helper::getExpressionDate($departureDate);
            $model->toDate = Helper::getExpressionDate($returnedDate);
            $model->destination = $destination;
            $model->purpose = $purpose;
            $model->requestedAmount = $requestedAmt;
            $model->departureDate = Helper::getExpressionDate($departureDate);
            $model->returnedDate = Helper::getExpressionDate($returnedDate);
            $model->advanceAmount = $advanceAmount;
            if (isset($travelId) && $travelId == 0) {
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->travelCode = "";
                $model->requestedType = 'ep';
                $model->approverRole = $approverRole;
                $this->repository->add($model);
            } else if (isset($travelId) && $travelId > 0) {
                $this->repository->edit($model, $travelId);
            } else {
                return $this->redirect()->toRoute("travelRequest");
            }
            foreach ($expenseDtlList as $expenseDtl) {
                $transportType = $expenseDtl['transportType'];
                $id = (int) $expenseDtl['id'];
                $expenseDtlModel->departureDate = Helper::getExpressionDate($expenseDtl['departureDate']);
                $expenseDtlModel->departurePlace = $expenseDtl['departurePlace'];
                $expenseDtlModel->departureTime = Helper::getExpressionTime($expenseDtl['departureTime']);
                $expenseDtlModel->destinationDate = Helper::getExpressionDate($expenseDtl['destinationDate']);
                $expenseDtlModel->destinationPlace = $expenseDtl['destinationPlace'];
                $expenseDtlModel->destinationTime = Helper::getExpressionTime($expenseDtl['destinationTime']);
                $expenseDtlModel->transportType = $transportType['id'];
                $expenseDtlModel->fare = (float) $expenseDtl['fare'];
                $expenseDtlModel->allowance = ($expenseDtl['allowance'] != null) ? (float) $expenseDtl['allowance'] : null;
                $expenseDtlModel->localConveyence = ($expenseDtl['localConveyence'] != null) ? (float) $expenseDtl['localConveyence'] : null;
                $expenseDtlModel->miscExpenses = ($expenseDtl['miscExpense'] != null) ? (float) $expenseDtl['miscExpense'] : null;
                $expenseDtlModel->totalAmount = (float) $expenseDtl['total'];
                $expenseDtlModel->remarks = ($expenseDtl['remarks'] != null) ? $expenseDtl['remarks'] : null;
                $expenseDtlModel->status = 'E';
                $expenseDtlModel->fareFlag = ($expenseDtl['fareFlag'] == "true" && $expenseDtl['fareFlag'] != "") ? 'Y' : 'N';
                $expenseDtlModel->allowanceFlag = ($expenseDtl['allowanceFlag'] == "true" && $expenseDtl['allowanceFlag'] != "") ? 'Y' : 'N';
                $expenseDtlModel->localConveyenceFlag = ($expenseDtl['localConveyenceFlag'] == "true" && $expenseDtl['localConveyenceFlag'] != "") ? 'Y' : 'N';
                $expenseDtlModel->miscExpensesFlag = ($expenseDtl['miscExpenseFlag'] == "true" && $expenseDtl['miscExpenseFlag'] != "") ? 'Y' : 'N';
                if ($id == 0) {
                    $expenseDtlModel->id = ((int) Helper::getMaxId($this->adapter, TravelExpenseDetail::TABLE_NAME, TravelExpenseDetail::ID)) + 1;
                    $expenseDtlModel->travelId = ($requestedType == 'ad') ? $model->travelId : $travelId;
                    $expenseDtlModel->createdBy = $this->employeeId;
                    $expenseDtlModel->createdDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->add($expenseDtlModel);
                } else {
                    $expenseDtlModel->modifiedBy = (int) $this->employeeId;
                    $expenseDtlModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->edit($expenseDtlModel, $id);
                }
            }
            try {
                HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            return new CustomViewModel(['success' => true, 'data' => ['msg' => 'Travel Request Successfully added!!!']]);
        } else {
            $id = (int) $this->params()->fromRoute('id');
            $currentRequestType = 'ep';
            if ($id === 0) {
                $id = 0;
                $currentRequestType = 'ad';
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'currentRequestType' => $currentRequestType
            ]);
        }
    }

    public function deleteExpenseDetailAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $id = $postData['data']['id'];
            $repository = new TravelExpenseDtlRepository($this->adapter);
            $repository->delete($id);
            $responseData = [
                "success" => true,
                "data" => "Expense Detail Successfully Removed"
            ];
        } else {
            $responseData = [
                "success" => false,
            ];
        }
        return new CustomViewModel($responseData);
    }

    public function ExpenseDetailListAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy()['data'];
            $travelId = $postData['travelId'];
            $travelDetail = $this->repository->fetchById($travelId);
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlList = [];
            $result = $expenseDtlRepo->fetchByTravelId($travelId);
            foreach ($result as $row) {
                array_push($expenseDtlList, $row);
            }
            return new CustomViewModel([
                'success' => true,
                'data' => [
                    'travelDetail' => $travelDetail,
                    'expenseDtlList' => $expenseDtlList,
                    'numExpenseDtlList' => count($expenseDtlList)
                ]
            ]);
        } else {
            return new CustomViewModel(['success' => false]);
        }
    }

    public function viewExpenseAction() {
        $this->initializeForm();
        $this->getRecommendApprover();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $empRepository = new EmployeeRepository($this->adapter);
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);

        $model = new TravelRequestModel();
        $detail = $this->repository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
        $approved_by = $fullName($detail['APPROVED_BY']);

        $recommenderName = $approverName;
        $empRepository = new EmployeeRepository($this->adapter);
        $approverFlag = ($detail['APPROVER_ROLE'] == 'DCEO') ? [HrEmployees::IS_DCEO => 'Y'] : [HrEmployees::IS_CEO => 'Y'];
        $whereCondition = array_merge([HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], $approverFlag);
        $approverDetail = $empRepository->fetchByCondition($whereCondition);
        $approverName = ($approverDetail != null) ? $approverDetail['FIRST_NAME'] . " " . $approverDetail['MIDDLE_NAME'] . " " . $approverDetail['LAST_NAME'] : "";

        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        if ($detail['REFERENCE_TRAVEL_ID'] != null) {
            $referenceTravelDtl = $this->repository->fetchById($detail['REFERENCE_TRAVEL_ID']);
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

        $empDtl = $empRepository->fetchForProfileById($detail['EMPLOYEE_ID']);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes' => $requestType,
                    'employeeName' => $employeeName,
                    'status' => $detail['STATUS'],
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'advanceAmt' => $advanceAmt,
                    'expenseDtlList' => $expenseDtlList,
                    'transportType' => $transportType,
                    'todayDate' => date('d-M-Y'),
                    'detail' => $detail,
                    'empDtl' => $empDtl,
                    'totalExpense' => $totalExpense
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('travelRequest');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Travel Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('travelRequest');
    }

    public function viewAction() {
        $this->initializeForm();
        $this->getRecommendApprover();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);

        $model = new TravelRequestModel();
        $detail = $this->repository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
        $approved_by = $fullName($detail['APPROVED_BY']);

        $recommenderName = $approverName;
        $empRepository = new EmployeeRepository($this->adapter);
        $approverFlag = ($detail['APPROVER_ROLE'] == 'DCEO') ? [HrEmployees::IS_DCEO => 'Y'] : [HrEmployees::IS_CEO => 'Y'];
        $whereCondition = array_merge([HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], $approverFlag);
        $approverDetail = $empRepository->fetchByCondition($whereCondition);
        $approverName = ($approverDetail != null) ? $approverDetail['FIRST_NAME'] . " " . $approverDetail['MIDDLE_NAME'] . " " . $approverDetail['LAST_NAME'] : "";

        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        $transportTypes = array(
            'AP' => 'Flight',
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
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $subDetail = [];
        if ($detail['SUB_EMPLOYEE_ID'] != null) {
            $subEmpDetail = $empRepository->fetchForProfileById($detail['SUB_EMPLOYEE_ID']);
            $subDetail = [
                'SUB_EMPLOYEE_NAME' => $fullName($detail['SUB_EMPLOYEE_ID']),
                'SUB_DESIGNATION' => $subEmpDetail['DESIGNATION'],
                'SUB_APPROVED_DATE' => $detail['SUB_APPROVED_DATE']
            ];
        }
        $fromDate = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $detail['FROM_DATE']);
        $toDate = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $detail['TO_DATE']);
        $interval = $fromDate->diff($toDate);
        $duration = $interval->format('%a') + 1;
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes' => $requestType,
                    'employeeName' => $employeeName,
                    'status' => $detail['STATUS'],
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'transportTypes' => $transportTypes,
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
                    'customRender' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", false, true)
        ]);
    }

    public function recommendApproveList() {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $this->employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach ($designations as $key => $designationList) {
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch, $withinDepartment, $designationId, $branchId, $departmentId);

            if ($key == 1) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            } else if ($key == 2) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver" => $approver
        ];
        return $responseData;
    }

}
