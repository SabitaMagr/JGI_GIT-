<?php

namespace WorkOnDayoff\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use WorkOnDayoff\Repository\WorkOnDayoffStatusRepository;
use ManagerService\Repository\DayoffWorkApproveRepository;
use SelfService\Repository\WorkOnDayoffRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\WorkOnDayoffForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\WorkOnDayoff;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use LeaveManagement\Repository\LeaveAssignRepository;

class WorkOnDayoffStatus extends AbstractActionController {

    private $adapter;
    private $dayoffWorkApproveRepository;
    private $workonDayoffStatusRepository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->dayoffWorkApproveRepository = new DayoffWorkApproveRepository($adapter);
        $this->workonDayoffStatusRepository = new WorkOnDayoffStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnDayoffForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $status = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C' => 'Cancelled'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("workOnDayoffStatus");
        }
        $workOnDayoffModel = new WorkOnDayoff();
        $request = $this->getRequest();

        $detail = $this->dayoffWorkApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
            $recommApprove = 1;
        }

        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $RECM_MN = ($detail['RECM_MN'] != null) ? " " . $detail['RECM_MN'] . " " : " ";
        $recommender = $detail['RECM_FN'] . $RECM_MN . $detail['RECM_LN'];
        $APRV_MN = ($detail['APRV_MN'] != null) ? " " . $detail['APRV_MN'] . " " : " ";
        $approver = $detail['APRV_FN'] . $APRV_MN . $detail['APRV_LN'];
        $MN1 = ($detail['MN1'] != null) ? " " . $detail['MN1'] . " " : " ";
        $recommended_by = $detail['FN1'] . $MN1 . $detail['LN1'];
        $MN2 = ($detail['MN2'] != null) ? " " . $detail['MN2'] . " " : " ";
        $approved_by = $detail['FN2'] . $MN2 . $detail['LN2'];
        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommender : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'C' || $status == 'RQ' || ($status == 'R' && $approvedDT == null)) ? $approver : $approved_by;


        if (!$request->isPost()) {
            $workOnDayoffModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnDayoffModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $workOnDayoffModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $workOnDayoffModel->status = "R";
                $this->flashmessenger()->addMessage("Work on Day-off Request Rejected!!!");
            } else if ($action == "Approve") {
                $this->wodApproveAction($detail);
                $workOnDayoffModel->status = "AP";
                $this->flashmessenger()->addMessage("Work on Day-off Request Approved");
            }
            $workOnDayoffModel->approvedBy = $this->employeeId;
            $workOnDayoffModel->approvedRemarks = $reason;
            $this->dayoffWorkApproveRepository->edit($workOnDayoffModel, $id);

            return $this->redirect()->toRoute("workOnDayoffStatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approvedDT' => $detail['APPROVED_DATE'],
                    'approver' => $authApprover,
                    'status' => $status,
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove' => $recommApprove
        ]);
    }

    private function wodApproveAction($detail) {
        $this->dayoffWorkApproveRepository->wodReward($detail['ID']);
    }

}
