<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */
namespace SelfService\Controller;

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
use LeaveManagement\Model\LeaveMaster;
use Zend\Form\Element\Select;

class LeaveRequest extends AbstractActionController{

    private $leaveRequestRepository;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
        $this->adapter = $adapter;

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm(){
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
        $leaveRequest = $this->leaveRequestRepository->selectAll($this->employeeId);
        return Helper::addFlashMessagesToArray($this, ['leaveRequest' => $leaveRequest]);
    }
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaveFormElement->setLabel("Leave");
        $leaveFormElement->setValueOptions($this->leaveRequestRepository->getLeaveList($this->employeeId));
        $leaveFormElement->setAttributes(["id" => "leaveId", "ng-model"=>"leaveId", "ng-change"=>"change()", "class" => "form-control"]);

        if($request->isPost()){
            $this->form->setData($request->getPost());

            if($this->form->isValid()){
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID)+1;
                $leaveRequest->employeeId=$this->employeeId;
                $leaveRequest->startDate=Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate=Helper::getExpressionDate($leaveRequest->endDate);
                $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "RQ";

                $this->leaveRequestRepository->add($leaveRequest);
                $this->flashmessenger()->addMessage("Leave Request Successfully added!!!");
                return $this->redirect()->toRoute("leaverequest");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'employeeId'=>$this->employeeId,
            'leave' => $this->leaveRequestRepository->getLeaveList($this->employeeId),
            'customRenderer'=>Helper::renderCustomView(),
        ]);
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('leaverequest');
        }
        $this->leaveRequestRepository->delete($id);
        $this->flashmessenger()->addMessage("Leave Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('leaverequest');
    }

}