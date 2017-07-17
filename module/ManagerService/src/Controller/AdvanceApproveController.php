<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\AdvanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest;
use SelfService\Repository\AdvanceRequestRepository;
use Setup\Model\Advance;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

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
        //print_r($this->employeeId); die();
        $list = $this->advanceApproveRepository->getAllRequest($this->employeeId);

        $advanceApprove = [];
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
                'ADVANCE_DATE' => $row['ADVANCE_DATE'],
                'REQUESTED_AMOUNT' => $row['REQUESTED_AMOUNT'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REASON' => $row['REASON'],
                'TERMS' => $row['TERMS'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'ADVANCE_NAME' => $row['ADVANCE_NAME'],
                'ADVANCE_REQUEST_ID' => $row['ADVANCE_REQUEST_ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }

            array_push($advanceApprove, $dataArray);
        }
        //print_r($advanceApprove); die();
        return Helper::addFlashMessagesToArray($this, ['advanceApprove' => $advanceApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();
        $advanceRequestRepository = new AdvanceRequestRepository($this->adapter);

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

}
