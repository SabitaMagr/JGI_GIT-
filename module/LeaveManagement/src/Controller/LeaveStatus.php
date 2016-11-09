<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:04 PM
 */
namespace LeaveManagement\Controller;

use Application\Helper\Helper;
use LeaveManagement\Repository\LeaveStatusRepository;
use ManagerService\Repository\LeaveApproveRepository;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use LeaveManagement\Form\LeaveApplyForm;
use Zend\Form\Annotation\AnnotationBuilder;
use LeaveManagement\Model\LeaveApply;

class LeaveStatus extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new LeaveStatusRepository($adapter);
        $this->adapter = $adapter;
    }
    public function initializeForm(){
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
        $pendingList = $this->repository->getAllRequest('RQ');
        $recommendedList = $this->repository->getAllRequest('RC');
        $approvedList = $this->repository->getAllRequest('AP');
        $rejectedList = $this->repository->getAllRequest('R');
        return Helper::addFlashMessagesToArray($this,[
            'pendingList'=>$pendingList,
            'recommendedList'=>$recommendedList,
            'approvedList'=>$approvedList,
            'rejectedList'=>$rejectedList
        ]);
    }

    public function viewAction(){
        $this->initializeForm();
        $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if($id===0){
            return $this->redirect()->toRoute("leavestatus");
        }
        $leaveApply = new LeaveApply();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        $employeeName = $detail['FIRST_NAME']." ".$detail['MIDDLE_NAME']." ".$detail['LAST_NAME'];
        $recommender = $detail['FN1']." ".$detail['MN1']." ".$detail['LN1'];
        $approver = $detail['FN2']." ".$detail['MN2']." ".$detail['LN2'];
        $status = $detail['STATUS'];

        //to get the previous balance of selected leave from assigned leave detail
        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'],$detail['EMPLOYEE_ID'])->getArrayCopy();
        $preBalance = $result['BALANCE'];

        if(!$request->isPost()){
            $leaveApply->exchangeArrayFromDB($detail);
            $this->form->bind($leaveApply);
        }else{
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $leaveApply->approvedDt=Helper::getcurrentExpressionDate();
            if($action=="Reject"){
                $leaveApply->status="R";
                $this->flashmessenger()->addMessage("Leave Request Rejected!!!");
            }else if($action=="Approve"){
                $leaveApply->status="AP";

                $newBalance = $preBalance-$detail['NO_OF_DAYS'];
                //to update the previous balance
                $leaveApproveRepository->updateLeaveBalance($detail['LEAVE_ID'],$detail['EMPLOYEE_ID'],$newBalance);

                $this->flashmessenger()->addMessage("Leave Request Approved");
            }
            unset($leaveApply->halfDay);
            $leaveApply->approvedRemarks = $reason;
            $leaveApproveRepository->edit($leaveApply,$id);

            return $this->redirect()->toRoute("leavestatus");
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id'=>$id,
            'employeeName'=>$employeeName,
            'requestedDt'=>$detail['REQUESTED_DT'],
            'availableDays'=>$preBalance,
            'totalDays'=>$result['TOTAL_DAYS'],
            'recommender'=>$recommender,
            'approver'=>$approver,
            'remarkDtl'=>$detail['REMARKS'],
            'status'=>$status,
            'leave' => $leaveRequestRepository->getLeaveList($detail['EMPLOYEE_ID']),
            'customRenderer'=>Helper::renderCustomView()
        ]);
    }
}