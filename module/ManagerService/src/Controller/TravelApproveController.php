<?php

namespace ManagerService\Controller;

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
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TravelApproveController extends AbstractActionController {

    private $travelApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $travelApprove = $this->getAllList();
        return Helper::addFlashMessagesToArray($this, ['travelApprove' => $travelApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $travelRequestModel = new TravelRequest();
        $request = $this->getRequest();

        $detail = $this->travelApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
        if (!$request->isPost()) {
            $travelRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($travelRequestModel);
        } else {
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
                $this->travelApproveRepository->edit($travelRequestModel, $id);
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
                $this->travelApproveRepository->edit($travelRequestModel, $id);
                $travelRequestModel->travelId = $id;

                // to update back date changes
//                $sDate = $detail['FROM_DATE'];
//                $eDate = $detail['TO_DATE'];
//                $currDate = Helper::getCurrentDate();
//                $begin = new DateTime($sDate);
//                $end = new DateTime($eDate);
//                $attendanceDetailModel = new AttendanceDetail();
//                $attendanceDetailModel->travelId = $detail['TRAVEL_ID'];
//                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                //                start of transaction
//                $connection = $this->adapter->getDriver()->getConnection();
//                $connection->beginTransaction();
//                try {
//                    if (strtotime($sDate) <= strtotime($currDate)) {
//                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
//                            $travelDate = $i->format("d-M-Y");
//                            if (strtotime($travelDate) <= strtotime($currDate)) {
//                                $where = ["EMPLOYEE_ID" => $requestedEmployeeID, "ATTENDANCE_DT" => $travelDate];
//                                $attendanceDetailRepo->editWith($attendanceDetailModel, $where);
//                            }
//                        }
//                    }
//                    $travelRequestModel->approvedRemarks = $getData->approvedRemarks;
//                    $this->travelApproveRepository->edit($travelRequestModel, $id);
//                    $travelRequestModel->travelId = $id;
//                    $connection->commit();
//                } catch (exception $e) {
//                    $connection->rollback();
//                    echo "error message:" . $e->getMessage();
//                }
//                end of transaction
                try {
                    HeadNotification::pushNotification(($travelRequestModel->status == 'AP') ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED, $travelRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("travelApprove");
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

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

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
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'requestType' => $requestType,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'status' => $status,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'employeeId' => $this->employeeId,
                    'advanceAmt' => $advanceAmt,
                    'transportTypes' => $transportTypes,
                    'requestedEmployeeId' => $requestedEmployeeID,
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
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $detail = $this->travelApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];

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
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'status' => $status,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'employeeId' => $this->employeeId,
                    'advanceAmt' => $advanceAmt,
                    'expenseDtlList' => $expenseDtlList,
                    'transportType' => $transportType,
                    'requestedEmployeeId' => $requestedEmployeeID,
                    'todayDate' => date('d-M-Y'),
                    'detail' => $detail,
                    'empDtl' => $empDtl,
                    'totalExpense' => $totalExpense
                        ]
        );
    }

    public function statusAction() {
        $travelStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $travelStatusFormElement = new Select();
        $travelStatusFormElement->setName("travelStatus");
        $travelStatusFormElement->setValueOptions($travelStatus);
        $travelStatusFormElement->setAttributes(["id" => "travelRequestStatusId", "class" => "form-control"]);
        $travelStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $travelStatusFormElement,
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
                        $travelRequestModel = new TravelRequest();
                        $id = $data['id'];
                        $role = $data['role'];
                        $detail = $this->travelApproveRepository->fetchById($id);

                        if ($role == 2) {
                            $travelRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            $travelRequestModel->recommendedBy = (int) $this->employeeId;
                            if ($action == "Reject") {
                                $travelRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $travelRequestModel->status = "RC";
                            }
                            $this->travelApproveRepository->edit($travelRequestModel, $id);
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

                            $this->travelApproveRepository->edit($travelRequestModel, $id);
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

    public function getAllList() {
        $list = $this->travelApproveRepository->getAllRequest($this->employeeId);

        $travelApprove = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getStatusValue = function($status) {
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
        $getRole = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 2;
            } else if ($this->employeeId == $approver) {
                return 3;
            }
        };
        $getRequestedType = function($requestedType) {
            if ($requestedType == 'ad') {
                return 'Advance';
            } else if ($requestedType == 'ep') {
                return 'Expense';
            }
        };
        foreach ($list as $row) {
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            $dataArray = [
                'FULL_NAME' => $row['FULL_NAME'],
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'FROM_DATE' => $row['FROM_DATE'],
                'TO_DATE' => $row['TO_DATE'],
                'DESTINATION' => $row['DESTINATION'],
                'PURPOSE' => $row['PURPOSE'],
                'REQUESTED_TYPE' => $getRequestedType($row['REQUESTED_TYPE']),
                'REQUESTED_AMOUNT' => $row['REQUESTED_AMOUNT'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REMARKS' => $row['REMARKS'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'TRAVEL_ID' => $row['TRAVEL_ID'],
                'TRAVEL_CODE' => $row['TRAVEL_CODE'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'TO_DATE_N' => $row['TO_DATE_N'],
                'FROM_DATE_N' => $row['FROM_DATE_N'],
                'REQUESTED_DATE_N' => $row['REQUESTED_DATE_N'],
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($travelApprove, $dataArray);
        }
        return $travelApprove;
    }

}
