<?php

namespace ManagerService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Repository\OvertimeDetailRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class OvertimeApproveController extends HrisController {

    private $overtimeDetailRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(\Overtime\Repository\OvertimeStatusRepository::class);
        $this->initializeForm(OvertimeRequestForm::class);
        $this->overtimeDetailRepository = new OvertimeDetailRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $list = $this->getAllList();
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

        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeApprove");
        }
        $overtimeModel = new Overtime();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];
        $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($detail['OVERTIME_ID']);
		//print_r($overtimeDetailResult); die;
        $overtimeDetails = [];
        foreach ($overtimeDetailResult as $overtimeDetailRow) {
            array_push($overtimeDetails, $overtimeDetailRow);
        }
		//print_r($overtimeDetails); die;
        if ($request->isPost()) {
            $postedData = (array) $request->getPost();
            $action = $postedData['submit'];
            $this->makeDecision($id, $role, $action == 'Approve', $postedData[$role == 2 ? 'recommendedRemarks' : 'approvedRemarks'], true);
            return $this->redirect()->toRoute("overtimeApprove");
        }
        $overtimeModel->exchangeArrayFromDB($detail);
        $this->form->bind($overtimeModel);
		
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
                    'totalHour' => $detail['TOTAL_HOUR_DETAIL']
        ]);
    }

    public function statusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $result = $this->repository->getFilteredRecord($data, $data['recomApproveId']);
                $recordList = [];
                foreach ($result as $row) {
                    $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($row['OVERTIME_ID']);
                    $overtimeDetails = Helper::extractDbData($overtimeDetailResult);
                    $row['DETAILS'] = $overtimeDetails;
                    array_push($recordList, $row);
                }
                return new JsonModel(["success" => "true", "data" => $recordList]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'requestStatusId', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusSE,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            $this->makeDecision($postData['id'], $postData['role'], $postData['btnAction'] == "btnApprove");
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $role, $approve, $remarks = null, $enableFlashNotification = false) {
        $notificationEvent = null;
        $message = null;
        $model = new Overtime();
        $model->overtimeId = $id;
        switch ($role) {
            case 2:
                $model->recommendedRemarks = $remarks;
                $model->recommendedDate = Helper::getcurrentExpressionDate();
                $model->recommendedBy = $this->employeeId;
                $model->status = $approve ? "RC" : "R";
                $message = $approve ? "Overtime Request Recommended" : "Overtime Request Rejected";
                $notificationEvent = $approve ? NotificationEvents::OVERTIME_RECOMMEND_ACCEPTED : NotificationEvents::OVERTIME_RECOMMEND_REJECTED;
                break;
            case 4:
                $model->recommendedDate = Helper::getcurrentExpressionDate();
                $model->recommendedBy = $this->employeeId;
            case 3:
                $model->approvedRemarks = $remarks;
                $model->approvedDate = Helper::getcurrentExpressionDate();
                $model->approvedBy = $this->employeeId;
                $model->status = $approve ? "AP" : "R";
                $message = $approve ? "Overtime Request Approved" : "Overtime Request Rejected";
                $notificationEvent = $approve ? NotificationEvents::OVERTIME_APPROVE_ACCEPTED : NotificationEvents::OVERTIME_APPROVE_REJECTED;
                break;
        }
        $this->repository->edit($model, $id);
        if ($enableFlashNotification) {
            $this->flashmessenger()->addMessage($message);
        }
        try {
            HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
    }

    public function getAllList() {
        $list = $this->repository->getAllRequest($this->employeeId);
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
