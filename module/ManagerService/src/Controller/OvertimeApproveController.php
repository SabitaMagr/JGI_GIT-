<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\OvertimeApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Repository\OvertimeDetailRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class OvertimeApproveController extends AbstractActionController {

    private $overtimeApproveRepository;
    private $overtimeDetailRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeApproveRepository = new OvertimeApproveRepository($adapter);
        $this->overtimeDetailRepository = new OvertimeDetailRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $overtimeRequest = $this->getAllList();
        return Helper::addFlashMessagesToArray($this, ['overtimeRequest' => $overtimeRequest, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeApprove");
        }
        $overtimeModel = new Overtime();
        $request = $this->getRequest();

        $detail = $this->overtimeApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
        $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($detail['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach ($overtimeDetailResult as $overtimeDetailRow) {
            array_push($overtimeDetails, $overtimeDetailRow);
        }
        if (!$request->isPost()) {
            $overtimeModel->exchangeArrayFromDB($detail);
            $this->form->bind($overtimeModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $overtimeModel->recommendedDate = Helper::getcurrentExpressionDate();
                $overtimeModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $overtimeModel->status = "R";
                    $this->flashmessenger()->addMessage("Overtime Request Rejected!!!");
                } else if ($action == "Approve") {
                    $overtimeModel->status = "RC";
                    $this->flashmessenger()->addMessage("Overtime Request Approved!!!");
                }
                $overtimeModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->overtimeApproveRepository->edit($overtimeModel, $id);
                try {
                    $overtimeModel->overtimeId = $id;
                    HeadNotification::pushNotification(($overtimeModel->status == 'RC') ? NotificationEvents::OVERTIME_RECOMMEND_ACCEPTED : NotificationEvents::OVERTIME_RECOMMEND_REJECTED, $overtimeModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $overtimeModel->approvedDate = Helper::getcurrentExpressionDate();
                $overtimeModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $overtimeModel->status = "R";
                    $this->flashmessenger()->addMessage("Overtime Request Rejected!!!");
                } else if ($action == "Approve") {
                    $overtimeModel->status = "AP";
                    $this->flashmessenger()->addMessage("Overtime Request Approved");
                }
                if ($role == 4) {
                    $overtimeModel->recommendedBy = $this->employeeId;
                    $overtimeModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $overtimeModel->approvedRemarks = $getData->approvedRemarks;
                $this->overtimeApproveRepository->edit($overtimeModel, $id);
                try {
                    $overtimeModel->overtimeId = $id;
                    HeadNotification::pushNotification(($overtimeModel->status == 'AP') ? NotificationEvents::OVERTIME_APPROVE_ACCEPTED : NotificationEvents::OVERTIME_APPROVE_REJECTED, $overtimeModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("overtimeApprove");
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
                    'overtimeDetails' => $overtimeDetails,
                    'totalHour' => $detail['TOTAL_HOUR']
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
                foreach ($postData as $data) {
                    $overtimeModel = new Overtime();
                    $id = $data['id'];
                    $role = $data['role'];

                    if ($role == 2) {
                        $overtimeModel->recommendedDate = Helper::getcurrentExpressionDate();
                        $overtimeModel->recommendedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $overtimeModel->status = "R";
                        } else if ($action == "Approve") {
                            $overtimeModel->status = "RC";
                        }
                        $this->overtimeApproveRepository->edit($overtimeModel, $id);
                        try {
                            $overtimeModel->overtimeId = $id;
                            HeadNotification::pushNotification(($overtimeModel->status == 'RC') ? NotificationEvents::OVERTIME_RECOMMEND_ACCEPTED : NotificationEvents::OVERTIME_RECOMMEND_REJECTED, $overtimeModel, $this->adapter, $this);
                        } catch (Exception $e) {
                            $this->flashmessenger()->addMessage($e->getMessage());
                        }
                    } else if ($role == 3 || $role == 4) {
                        $overtimeModel->approvedDate = Helper::getcurrentExpressionDate();
                        $overtimeModel->approvedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $overtimeModel->status = "R";
                        } else if ($action == "Approve") {
                            $overtimeModel->status = "AP";
                        }
                        if ($role == 4) {
                            $overtimeModel->recommendedBy = $this->employeeId;
                            $overtimeModel->recommendedDate = Helper::getcurrentExpressionDate();
                        }
                        $this->overtimeApproveRepository->edit($overtimeModel, $id);
                        try {
                            $overtimeModel->overtimeId = $id;
                            HeadNotification::pushNotification(($overtimeModel->status == 'AP') ? NotificationEvents::OVERTIME_APPROVE_ACCEPTED : NotificationEvents::OVERTIME_APPROVE_REJECTED, $overtimeModel, $this->adapter, $this);
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
        $list = $this->overtimeApproveRepository->getAllRequest($this->employeeId);
        $overtimeRequest = [];
        foreach ($list as $row) {
            $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($row['OVERTIME_ID']);
            $overtimeDetails = [];
            foreach ($overtimeDetailResult as $overtimeDetailRow) {
                array_push($overtimeDetails, $overtimeDetailRow);
            }
            $row['DETAILS'] = $overtimeDetails;
            array_push($overtimeRequest, $row);
        }
        return $overtimeRequest;
    }

}
