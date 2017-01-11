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
use LeaveManagement\Model\LeaveMaster;

class LeaveRequest extends AbstractActionController {

    private $leaveRequestRepository;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;
    private $adapter;
    private $recommender;
    private $approver;

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
    public function getRecommendApprover(){
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);

        if ($empRecommendApprove != null) {
            $this->recommender = $empRecommendApprove['RECOMMEND_BY'];
            $this->approver = $empRecommendApprove['APPROVED_BY'];
        } else {
            $result = $this->recommendApproveList();
            if(count($result['recommender'])>0){
                $this->recommender=$result['recommender'][0]['id'];
            }else{
                $this->recommender=null;
            }
            if(count($result['approver'])>0){
                $this->approver=$result['approver'][0]['id'];
            }else{
                 $this->approver=null;
            } 
        }               
    }

    public function indexAction() {
        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaves = \Application\Helper\EntityHelper::getTableKVList($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E']);
        $leaves[-1] = "All";
        ksort($leaves);
        $leaveFormElement->setValueOptions($leaves);
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control"]);
        $leaveFormElement->setLabel("Leave Type");
        
        $leaveStatus = [
            '-1'=>'All',
            'RQ'=>'Pending',
            'RC'=>'Recommended',
            'AP'=>'Approved',
            'R'=>'Rejected'
        ];
        $leaveStatusFormElement = new Select();
        $leaveStatusFormElement->setName("leaveStatus");
        $leaveStatusFormElement->setValueOptions($leaveStatus);
        $leaveStatusFormElement->setAttributes(["id" => "leaveRequestStatusId", "class" => "form-control"]);
        $leaveStatusFormElement->setLabel("Leave Request Status");

        return Helper::addFlashMessagesToArray($this, [
            'leaves' => $leaveFormElement,
            'leaveStatus'=>$leaveStatusFormElement,
            'employeeId'=>$this->employeeId
                ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaveFormElement->setLabel("Leave");
        $leaveFormElement->setValueOptions($this->leaveRequestRepository->getLeaveList($this->employeeId));
        $leaveFormElement->setAttributes(["id" => "leaveId", "ng-model" => "leaveId", "ng-change" => "change()", "class" => "form-control"]);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID) + 1;
                $leaveRequest->employeeId = $this->employeeId;
                $leaveRequest->startDate = Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate = Helper::getExpressionDate($leaveRequest->endDate);
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
        $this->getRecommendApprover();
        
        $id = (int) $this->params()->fromRoute('id');
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveapprove");
        }
        
        $fullName = function($id){
          $empRepository = new EmployeeRepository($this->adapter);
          $empDtl = $empRepository->fetchById($id);
          $empMiddleName = ($empDtl['MIDDLE_NAME']!=null)? " ".$empDtl['MIDDLE_NAME']." " :" ";
          return $empDtl['FIRST_NAME'].$empMiddleName.$empDtl['LAST_NAME'];
        };
        
        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);
        
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $leaveApproveRepository->fetchById($id);

        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);    
        $approved_by = $fullName($detail['APPROVED_BY']);
        $authRecommender = ($status=='RQ' || $status=='C')?$recommenderName:$recommended_by;
        $authApprover = ($status=='RC' || $status=='RQ' || $status=='C' || ($status=='R' && $approvedDT==null))?$approverName:$approved_by;       
        $employeeName = $fullName($detail['EMPLOYEE_ID']);
        
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
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
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
