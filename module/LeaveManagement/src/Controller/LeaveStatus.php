<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:04 PM
 */

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveMasterRepository;
use LeaveManagement\Repository\LeaveStatusRepository;
use ManagerService\Repository\LeaveApproveRepository;
use SelfService\Repository\LeaveRequestRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Repository\RecommendApproveRepository;
use SelfService\Repository\LeaveSubstituteRepository;
use Setup\Model\HrEmployees;


class LeaveStatus extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;
    private $userId;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveStatusRepository($adapter);
        $this->adapter = $adapter;
        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaves = EntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E'], LeaveMaster::LEAVE_ENAME, "ASC",NULL,FALSE,TRUE);
        $leaves1 = [-1 => "All Type"] + $leaves;
        $leaveFormElement->setValueOptions($leaves1);
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control"]);
        $leaveFormElement->setLabel("Type");

        $leaveStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C' => 'Cancelled'
        ];
        $leaveStatusFormElement = new Select();
        $leaveStatusFormElement->setName("leaveStatus");
        $leaveStatusFormElement->setValueOptions($leaveStatus);
        $leaveStatusFormElement->setAttributes(["id" => "leaveRequestStatusId", "class" => "form-control"]);
        $leaveStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'leaves' => $leaveFormElement,
                    'leaveStatus' => $leaveStatusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function viewAction() {
        $this->initializeForm();
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("leavestatus");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $leaveApproveRepository->fetchById($id);
        //print_r($detail); die();

        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if($empRecommendApprove['RECOMMEND_BY']==$empRecommendApprove['APPROVED_BY']){
            $recommApprove=1;
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

        $recommenderId = ($status == 'RQ') ? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        //to get the previous balance of selected leave from assigned leave detail
        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];
        
        $leaveSubstituteRepo = new LeaveSubstituteRepository($this->adapter);
        $leaveSubstituteDetail = $leaveSubstituteRepo->fetchById($detail['ID']);

        if (!$request->isPost()) {
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $leaveApply->status = "R";
                $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
            } else if ($action == "Approve") {
                $leaveApply->status = "AP";

                if ($detail['HALF_DAY'] != 'N') {
                    $leaveTaken = 0.5;
                } else {
                    $leaveTaken = $detail['NO_OF_DAYS'];
                }
                $newBalance = $preBalance - $leaveTaken;
                //to update the previous balance
                $leaveApproveRepository->updateLeaveBalance($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'], $newBalance);

                $this->flashmessenger()->addMessage("Leave Request Approved");
            }
            unset($leaveApply->halfDay);
            $leaveApply->approvedRemarks = $reason;
            $leaveApply->approvedBy = $this->employeeId;
            $leaveApproveRepository->edit($leaveApply, $id);

            return $this->redirect()->toRoute("leavestatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $requestedEmployeeID,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DT'],
                    'availableDays' => $preBalance,
                    'totalDays' => $result['TOTAL_DAYS'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'approvedDT' => $detail['APPROVED_DT'],
                    'remarkDtl' => $detail['REMARKS'],
                    'status' => $status,
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=> $recommApprove,
                    'subEmployeeId'=> $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks'=>$detail['SUB_REMARKS'],
                    'subApprovedFlag'=>$detail['SUB_APPROVED_FLAG'],
                    'employeeList'=>  EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],[HrEmployees::STATUS => "E",HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ",FALSE,TRUE)
        
        ]);
    }

}
