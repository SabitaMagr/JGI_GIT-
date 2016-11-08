<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 5:05 PM
 */

namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use ManagerService\Repository\LeaveApproveRepository;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use Zend\Form\Annotation\AnnotationBuilder;
use LeaveManagement\Model\LeaveApply;
use SelfService\Repository\LeaveRequestRepository;

class LeaveApproveController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new LeaveApproveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm(){
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
        //print_r($this->employeeId); die();
        $list = $this->repository->getAllRequest($this->employeeId);
        return Helper::addFlashMessagesToArray($this, ['list' => $list,'id'=>$this->employeeId]);
    }
    public function viewAction(){
        $this->initializeForm();
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if($id===0){
            return $this->redirect()->toRoute("leaveapprove");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        $employeeName = $detail['FIRST_NAME']." ".$detail['MIDDLE_NAME']." ".$detail['LAST_NAME'];

        //to get the previous balance of selected leave from assigned leave detail
        $result = $this->repository->assignedLeaveDetail($detail['LEAVE_ID'],$detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if(!$request->isPost()){
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        }else{
            $getData = $request->getPost();
            $action = $getData->submit;

            if($role=="R"){
                $leaveApply->recommendedDt=Helper::getcurrentExpressionDate();
                if($action=="Reject"){
                    $leaveApply->status="R";
                    $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
                }else if($action=="Approve"){
                    $leaveApply->status="RC";
                    $this->flashmessenger()->addMessage("Leave Request Approved!!!");
                }
                $leaveApply->recommendedRemarks=$getData->recommendedRemarks;
                $this->repository->edit($leaveApply,$id);
            }else if($role=="A"){
                $leaveApply->approvedDt=Helper::getcurrentExpressionDate();
                if($action=="Reject"){
                    $leaveApply->status="R";
                    $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
                }else if($action=="Approve"){
                    $leaveApply->status="AP";

                    $newBalance = $preBalance-$detail['NO_OF_DAYS'];
                    //to update the previous balance
                    $this->repository->updateLeaveBalance($detail['LEAVE_ID'],$detail['EMPLOYEE_ID'],$newBalance);

                    $this->flashmessenger()->addMessage("Leave Request Approved");
                }
                unset($leaveApply->halfDay);
                $leaveApply->approvedRemarks=$getData->approvedRemarks;
                $this->repository->edit($leaveApply,$id);
            }
            return $this->redirect()->toRoute("leaveapprove");
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id'=>$id,
            'employeeName'=>$employeeName,
            'requestedDt'=>$detail['REQUESTED_DT'],
            'role'=>$role,
            'availableDays'=>$preBalance,
            'status'=>$detail['STATUS'],
            'remarksDtl'=>$detail['REMARKS'],
            'totalDays'=>$result['TOTAL_DAYS'],
            'recommendedBy'=>$detail['RECOMMENDED_BY'],
            'employeeId'=>$this->employeeId,
            'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
            'customRenderer'=>Helper::renderCustomView()
        ]);
    }

    public function statusAction(){
        $pendingList = $this->repository->getAllRequest($this->employeeId);
        $recommendedList = $this->repository->getAllRequest($this->employeeId,'RC');
        $approvedList = $this->repository->getAllRequest($this->employeeId,'AP');
        $rejectedList = $this->repository->getAllRequest($this->employeeId,'R');

        return Helper::addFlashMessagesToArray($this, [
            'pendingList' => $pendingList,
            'id'=>$this->employeeId,
            'recommendedList'=>$recommendedList,
            'approvedList'=>$approvedList,
            'rejectedList'=>$rejectedList
        ]);
    }
}