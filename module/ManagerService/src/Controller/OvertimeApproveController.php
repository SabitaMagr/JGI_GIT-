<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use ManagerService\Repository\OvertimeApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\OvertimeRequestForm;
use Setup\Repository\RecommendApproveRepository;
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
        $list = $this->overtimeApproveRepository->getAllRequest($this->employeeId);

        $overtimeRequest = [];
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
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'OVERTIME_DATE' => $row['OVERTIME_DATE'],
                'DESCRIPTION' => $row['DESCRIPTION'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REMARKS' => $row['REMARKS'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'OVERTIME_ID' => $row['OVERTIME_ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($row['OVERTIME_ID']);
            $overtimeDetails = [];
            foreach($overtimeDetailResult as $overtimeDetailRow){
                array_push($overtimeDetails,$overtimeDetailRow);
            }
            $dataArray['DETAILS']=$overtimeDetails;
            array_push($overtimeRequest, $dataArray);
        }
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
        
        $overtimeDetailResult = $this->overtimeDetailRepository->fetchByOvertimeId($detail['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach($overtimeDetailResult as $overtimeDetailRow){
            array_push($overtimeDetails,$overtimeDetailRow);
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
                $overtimeModel->overtimeId = $id;
//                try {
//                    HeadNotification::pushNotification(($overtimeModel->status == 'RC') ? NotificationEvents::OVERTIME_RECOMMEND_ACCEPTED : NotificationEvents::OVERTIME_RECOMMEND_REJECTED, $overtimeModel, $this->adapter, $this->plugin('url'));
//                } catch (Exception $e) {
//                    $this->flashmessenger()->addMessage($e->getMessage());
//                }
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
                $overtimeModel->overtimeId = $id;
//                try {
//                    HeadNotification::pushNotification(($overtimeModel->status == 'AP') ? NotificationEvents::OVERTIME_APPROVE_ACCEPTED : NotificationEvents::OVERTIME_APPROVE_REJECTED, $overtimeModel, $this->adapter, $this->plugin('url'));
//                } catch (Exception $e) {
//                    $this->flashmessenger()->addMessage($e->getMessage());
//                }
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
                    'overtimeDetails'=>$overtimeDetails
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

}
