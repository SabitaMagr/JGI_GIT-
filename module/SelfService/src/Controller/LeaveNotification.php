<?php
namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Exception;
use SelfService\Model\LeaveSubstitute;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\EmployeeRepository;
use ManagerService\Repository\LeaveApproveRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use LeaveManagement\Form\LeaveApplyForm;
use Zend\Form\Annotation\AnnotationBuilder;
use LeaveManagement\Model\LeaveApply;
use SelfService\Repository\LeaveSubstituteRepository;
use SelfService\Repository\LeaveRequestRepository;
use Application\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;

class LeaveNotification extends AbstractActionController{
    private $adapter;
    private $repository;
    private $employeeId;
    private $form;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new LeaveSubstituteRepository($this->adapter);
    }
    
    public function indexAction() {
        $result = $this->repository->fetchByEmployeeId($this->employeeId);
        $list = [];
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
        
        $getValueApp = function($approvedFlag){
            if ($approvedFlag == "Y") {
                return "Yes";
            } else if ($approvedFlag == 'N') {
                return "No";
            } 
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        foreach($result as $row){
            $status = $getValue($row['STATUS']);
            
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DT'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];
            
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);
            $subEmployeeName = $fullName($row['SUB_EMPLOYEE_ID']);

            $new_row = array_merge($row, [
                'STATUS' => $status,
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'SUB_EMPLOYEE_NAME'=>$subEmployeeName,
                'SUB_APPROVED_FLAG'=>$getValueApp($row['SUB_APPROVED_FLAG'])
            ]);
            array_push($list, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, [
            'list'=>$list
        ]);
    }
    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }
    
     public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);
        
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
        
        $leaveSubstituteDetail = $this->repository->fetchById($id);

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveNotification");
        }

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $leaveApproveRepository->fetchById($id);
        $recommenderName = $fullName($detail['RECOMMENDER']);
        $approverName = $fullName($detail['APPROVER']);

        $leaveId = $detail['LEAVE_ID'];
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
        $approved_by = $fullName($detail['APPROVED_BY']);
        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;
        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        //to get the previous balance of selected leave from assigned leave detail
        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if (!$request->isPost()) {
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        }else {
            $leaveSubstitute = new LeaveSubstitute();
            $getData = $request->getPost();
            $action = $getData->submit;
            $leaveSubstitute->approvedDate = Helper::getcurrentExpressionDate();
            $leaveSubstitute->remarks = $getData->subRemarks;
            if($action=='Approve'){
                $leaveSubstitute->approvedFlag = "Y";
                $this->flashmessenger()->addMessage("Substitute Work Request Approved!!!");
            }else if($action=='Reject'){
                $leaveSubstitute->approvedFlag = "N";
                $leaveRequestRepository->delete($id);
                $this->flashmessenger()->addMessage("Substitute Work Request Rejected!!!");
            }
            $this->repository->edit($leaveSubstitute, $id);
            $leaveApply->id = $id;
            try {
                HeadNotification::pushNotification(($leaveSubstitute->approvedFlag == 'Y') ? NotificationEvents::LEAVE_SUBSTITUTE_ACCEPTED : NotificationEvents::LEAVE_SUBSTITUTE_REJECTED, $leaveApply, $this->adapter, $this->plugin('url'));
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            $this->redirect()->toRoute('leaveNotification');
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
                    'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
                    'subEmployeeId'=> $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks'=>$detail['SUB_REMARKS'],
                    'subApprovedFlag'=>$detail['SUB_APPROVED_FLAG'],
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList'=>  EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],[HrEmployees::STATUS => "E",HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ")
        ]);
    }
}