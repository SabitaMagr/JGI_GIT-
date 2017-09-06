<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\LoanApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\LoanRequestForm;
use SelfService\Model\LoanRequest;
use SelfService\Repository\LoanRequestRepository;
use Setup\Model\Loan;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class LoanApproveController extends AbstractActionController {

    private $loanApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->loanApproveRepository = new LoanApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new LoanRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $loanApprove = $this->getAllList();
        //print_r($loanApprove); die();
        return Helper::addFlashMessagesToArray($this, ['loanApprove' => $loanApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();
        $loanRequestRepository = new LoanRequestRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("loanApprove");
        }
        $loanRequestModel = new LoanRequest();
        $request = $this->getRequest();

        $detail = $this->loanApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $RECM_MN = ($detail['RECM_MN'] != null) ? " " . $detail['RECM_MN'] . " " : " ";
        $recommender = $detail['RECM_FN'] . $RECM_MN . $detail['RECM_LN'];
        $APRV_MN = ($detail['APRV_MN'] != null) ? " " . $detail['APRV_MN'] . " " : " ";
        $approver = $detail['APRV_FN'] . $APRV_MN . $detail['APRV_LN'];
        $MN1 = ($detail['MN1'] != null) ? " " . $detail['MN1'] . " " : " ";
        $recommended_by = $detail['FN1'] . $MN1 . $detail['LN1'];
        $MN2 = ($detail['MN2'] != null) ? " " . $detail['MN2'] . " " : " ";
        $approved_by = $detail['FN2'] . $MN2 . $detail['LN2'];
        $authRecommender = ($status == 'RQ') ? $recommender : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || ($status == 'R' && $approvedDT == null)) ? $approver : $approved_by;
        $recommenderId = ($status == 'RQ') ? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        if (!$request->isPost()) {
            $loanRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($loanRequestModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $loanRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $loanRequestModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $loanRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Loan Request Rejected!!!");
                } else if ($action == "Approve") {
                    $loanRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Loan Request Approved!!!");
                }
                $loanRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->loanApproveRepository->edit($loanRequestModel, $id);
                $loanRequestModel->loanRequestId = $id;
                try {
                    HeadNotification::pushNotification(($loanRequestModel->status == 'RC') ? NotificationEvents::LOAN_RECOMMEND_ACCEPTED : NotificationEvents::LOAN_RECOMMEND_REJECTED, $loanRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $loanRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $loanRequestModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $loanRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Loan Request Rejected!!!");
                } else if ($action == "Approve") {
                    $loanRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Loan Request Approved");
                }
                if ($role == 4) {
                    $loanRequestModel->recommendedBy = $this->employeeId;
                    $loanRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $loanRequestModel->approvedRemarks = $getData->approvedRemarks;
                $this->loanApproveRepository->edit($loanRequestModel, $id);
                $loanRequestModel->loanRequestId = $id;
                try {
                    HeadNotification::pushNotification(($loanRequestModel->status == 'AP') ? NotificationEvents::LOAN_APPROVE_ACCEPTED : NotificationEvents::LOAN_APPROVE_REJECTED, $loanRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("loanApprove");
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
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC", null, false, true)
        ]);
    }

    public function statusAction() {
        $loanFormElement = new Select();
        $loanFormElement->setName("loan");
        $loans = EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => 'E'], null, null, null, false, true);
        $loans1 = [-1 => "All"] + $loans;
        $loanFormElement->setValueOptions($loans1);
        $loanFormElement->setAttributes(["id" => "loanId", "class" => "form-control"]);
        $loanFormElement->setLabel("Loan Type");

        $loanStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $loanStatusFormElement = new Select();
        $loanStatusFormElement->setName("loanStatus");
        $loanStatusFormElement->setValueOptions($loanStatus);
        $loanStatusFormElement->setAttributes(["id" => "loanRequestStatusId", "class" => "form-control"]);
        $loanStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'loans' => $loanFormElement,
                    'loanStatus' => $loanStatusFormElement,
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
                    $loanRequestRepository = new LoanRequestRepository($this->adapter);

                    foreach ($postData as $data) {
                        $loanRequestModel = new LoanRequest();
                        $id = $data['id'];
                        $role = $data['role'];
//                        $detail = $this->loanApproveRepository->fetchById($id);

                        if ($role == 2) {
                            $loanRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            $loanRequestModel->recommendedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $loanRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $loanRequestModel->status = "RC";
                            }

                            $this->loanApproveRepository->edit($loanRequestModel, $id);
                            $loanRequestModel->loanRequestId = $id;
                            try {
                                HeadNotification::pushNotification(($loanRequestModel->status == 'RC') ? NotificationEvents::LOAN_RECOMMEND_ACCEPTED : NotificationEvents::LOAN_RECOMMEND_REJECTED, $loanRequestModel, $this->adapter, $this);
                            } catch (Exception $e) {
                                
                            }
                        } else if ($role == 3 || $role == 4) {
                            $loanRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                            $loanRequestModel->approvedBy = $this->employeeId;
                            if ($action == "Reject") {
                                $loanRequestModel->status = "R";
                            } else if ($action == "Approve") {
                                $loanRequestModel->status = "AP";
                            }
                            if ($role == 4) {
                                $loanRequestModel->recommendedBy = $this->employeeId;
                                $loanRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                            }
                            $this->loanApproveRepository->edit($loanRequestModel, $id);
                            $loanRequestModel->loanRequestId = $id;
                            try {
                                HeadNotification::pushNotification(($loanRequestModel->status == 'AP') ? NotificationEvents::LOAN_APPROVE_ACCEPTED : NotificationEvents::LOAN_APPROVE_REJECTED, $loanRequestModel, $this->adapter, $this);
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
        $list = $this->loanApproveRepository->getAllRequest($this->employeeId);
        $loanApprove = [];
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
        foreach ($list as $row) {
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            $dataArray = [
                'FULL_NAME' => $row['FULL_NAME'],
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'LOAN_DATE' => $row['LOAN_DATE'],
                'REQUESTED_AMOUNT' => $row['REQUESTED_AMOUNT'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REASON' => $row['REASON'],
                'LOAN_NAME' => $row['LOAN_NAME'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'LOAN_REQUEST_ID' => $row['LOAN_REQUEST_ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'REQUESTED_DATE_N' => $row['REQUESTED_DATE_N'],
                'LOAN_DATE_N' => $row['LOAN_DATE_N'],
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($loanApprove, $dataArray);
        }
        return $loanApprove;
    }

}
