<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */

namespace SelfService\Controller;

use ManagerService\Repository\LeaveApproveRepository;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\EntityHelper;
use Zend\Form\Element\Select;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\EmployeeRepository;
use LeaveManagement\Repository\LeaveMasterRepository;

class LeaveRequest extends AbstractActionController {

    private $leaveRequestRepository;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
        $this->adapter = $adapter;

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
        $leaveRequestList = $this->leaveRequestRepository->selectAll($this->employeeId);
        $leaveRequest = [];
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
            }
        };
        $getAction = function($status){
          if ($status == "RQ") {
                return ["delete"=>'Cancel Request'];
            }else{
                return ["view"=>'View'];
            }  
        };
        foreach ($leaveRequestList as $leaveRequestRow) {
            $status = $getValue($leaveRequestRow['STATUS']);
            $action = $getAction($leaveRequestRow['STATUS']);
            $new_row = array_merge($leaveRequestRow,['STATUS'=>$status,'ACTION'=>key($action),'ACTION_TEXT'=>$action[key($action)]]);
            array_push($leaveRequest, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['leaveRequest' => $leaveRequest]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);

        if ($empRecommendApprove != null) {
            $recommendBy = $empRecommendApprove['RECOMMEND_BY'];
            $approvedBy = $empRecommendApprove['APPROVED_BY'];
        } else {
            $result = $this->recommendApproveList();
            if(count($result['recommender'])>0){
                $recommendBy=$result['recommender'][0]['id'];
            }else{
                $recommendBy=null;
            }
            if(count($result['approver'])>0){
                $approvedBy=$result['approver'][0]['id'];
            }else{
                $approvedBy=null;
            } 
        }

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaveFormElement->setLabel("Leave");
        $leaveFormElement->setValueOptions($this->leaveRequestRepository->getLeaveList($this->employeeId));
        $leaveFormElement->setAttributes(["id" => "leaveId", "ng-model" => "leaveId", "ng-change" => "change()", "class" => "form-control"]);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            //print_R($request->getPost()); die();

            if ($this->form->isValid()) {
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID) + 1;
                $leaveRequest->employeeId = $this->employeeId;
                $leaveRequest->startDate = Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate = Helper::getExpressionDate($leaveRequest->endDate);
                $leaveRequest->recommendedBy = $recommendBy;
                $leaveRequest->approvedBy = $approvedBy;
                $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "RQ";

                $this->leaveRequestRepository->add($leaveRequest);
                $this->flashmessenger()->addMessage("Leave Request Successfully added!!!");
                return $this->redirect()->toRoute("leaverequest");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'leave' => $this->leaveRequestRepository->getLeaveList($this->employeeId),
                    'customRenderer' => Helper::renderCustomView(),
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('leaverequest');
        }
        $this->leaveRequestRepository->delete($id);
        $this->flashmessenger()->addMessage("Leave Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('leaverequest');
    }

    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveapprove");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $leaveApproveRepository->fetchById($id);

        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $recommender = $detail['FN1'] . " " . $detail['MN1'] . " " . $detail['LN1'];
        $approver = $detail['FN2'] . " " . $detail['MN2'] . " " . $detail['LN2'];

        //to get the previous balance of selected leave from assigned leave detail
        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if (!$request->isPost()) {
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DT'],
                    'availableDays' => $preBalance,
                    'status' => $detail['STATUS'],
                    'recommender' => $recommender,
                    'approver' => $approver,
                    'remarksDtl' => $detail['REMARKS'],
                    'totalDays' => $result['TOTAL_DAYS'],
                    'recommendedBy' => $detail['RECOMMENDED_BY'],
                    'employeeId' => $this->employeeId,
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'leave' => $this->leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
                    'customRenderer' => Helper::renderCustomView()
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
